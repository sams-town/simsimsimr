<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Purchase_order extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/pharmacy/Receipt_model", "Receipt");
        $this->load->model("v1/pharmacy/Receipt_po_comparison_model", "Receipt_po_comparison");
        $this->load->model("v1/pharmacy/Purchase_order_model", "Purchase_order");
    }

    public function ordering_get() {
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

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

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
            'Nama Obat/Alkes' => array(
                'width' => 15
            ),
            'Qty PO' => array(
                'width' => 15
            ),
            'Satuan' => array(
                'width' => 15
            ),
            'Harga/Pcs PO' => array(
                'width' => 15
            ),
            'Disc PO' => array(
                'width' => 15
            ),
            'Total Disc PO' => array(
                'width' => 15
            ),
            'Total Harga PO' => array(
                'width' => 15
            ),
            'Distributor' => array(
                'width' => 15
            ),
            'Tanggal PO' => array(
                'width' => 15
            ),
            'Keterangan' => array(
                'width' => 15
            ),
            'Tanggal Terima' => array(
                'width' => 15
            ),
            'Nama Penerima' => array(
                'width' => 15
            ),
            'Harga/Box' => array(
                'width' => 15
            ),
            'Qty/Box' => array(
                'width' => 15
            ),
            'Qty' => array(
                'width' => 15
            ),
            'Harga/Pcs' => array(
                'width' => 15
            ),
            'Disc' => array(
                'width' => 15
            ),
            'Total Disc' => array(
                'width' => 15
            ),
            'Total Harga' => array(
                'width' => 15
            ),
            'No. Faktur' => array(
                'width' => 15
            ),
            'Tanggal Faktur' => array(
                'width' => 15
            ),
            'TOP' => array(
                'width' => 15
            ),
            'Tanggal Jatuh Tempo' => array(
                'width' => 15
            ),
        );

        $column = 1;
        $row = 1;
        foreach ($headers as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getFont()->setBold(true);
            // color cell
            $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
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
        $purchase_order = $this->Purchase_order->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($purchase_order); $i++) { 
            $activeWorksheet->getstyle('A1')->applyFromArray($style);
            $activeWorksheet->getstyle('B1')->applyFromArray($style);
            $activeWorksheet->getstyle('C1')->applyFromArray($style);
            $activeWorksheet->getstyle('D1')->applyFromArray($style);
            $activeWorksheet->getstyle('E1')->applyFromArray($style);
            $activeWorksheet->getstyle('F1')->applyFromArray($style);
            $activeWorksheet->getstyle('G1')->applyFromArray($style);
            $activeWorksheet->getstyle('H1')->applyFromArray($style);
            $activeWorksheet->getstyle('I1')->applyFromArray($style);
            $activeWorksheet->getstyle('J1')->applyFromArray($style);
            $activeWorksheet->getstyle('K1')->applyFromArray($style);
            $activeWorksheet->getstyle('L1')->applyFromArray($style);
            $activeWorksheet->getstyle('M1')->applyFromArray($style);
            $activeWorksheet->getstyle('N1')->applyFromArray($style);
            $activeWorksheet->getstyle('O1')->applyFromArray($style);
            $activeWorksheet->getstyle('P1')->applyFromArray($style);
            $activeWorksheet->getstyle('Q1')->applyFromArray($style);
            $activeWorksheet->getstyle('R1')->applyFromArray($style);
            $activeWorksheet->getstyle('S1')->applyFromArray($style);
            $activeWorksheet->getstyle('T1')->applyFromArray($style);
            $activeWorksheet->getstyle('U1')->applyFromArray($style);
            $activeWorksheet->getstyle('V1')->applyFromArray($style);

            $purchase_order_id = $purchase_order[$i]->id;
            $pharmacy_catalog_id = $purchase_order[$i]->pharmacy_catalog_id;

            $nama_obat = $purchase_order[$i]->nama_obat;
            $qty_po = $purchase_order[$i]->qty;
            $satuan = $purchase_order[$i]->satuan;
            $harga_pcs_po = $purchase_order[$i]->harga_pcs;
            $disc_po = $purchase_order[$i]->disc;
            $total_disc_po = $purchase_order[$i]->total_disc;
            $total_harga_po = (float) $harga_pcs_po * (float) $qty_po - (float) $total_disc_po;
            $distributor = $purchase_order[$i]->distributor;
            $tgl_po = $purchase_order[$i]->tanggal_po;

            $select = 'a.id, a.receipt_id, a.total_po, a.total_receipt';
            $where = array(
                'purchase_order_id' => $purchase_order_id,
                'pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $join = array();
            $receipt_po_comparison = $this->Receipt_po_comparison->select(0, $select, $where, $join);

            $coordinate = $i + 2;

            if (count($receipt_po_comparison) > 0) {
                $receipt_id = $receipt_po_comparison[0]->receipt_id;
                $total_po = $receipt_po_comparison[0]->total_po;
                $total_receipt = $receipt_po_comparison[0]->total_receipt;

                $keterangan = '';

                if ((int) $total_po == (int) $total_receipt) {
                    $keterangan = 'Sukses';
                }

                $activeWorksheet->setCellValue('J' . $coordinate, $keterangan);

                $select = 'a.date_receipt as tanggal_terima, 
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
                $where = array(
                    'a.is_deleted' => 0,
                    'ri.pharmacy_catalog_id' => $pharmacy_catalog_id,
                );
                $join = array(
                    array('receipt_items ri', 'a.id = ri.receipt_id', 'INNER'),
                    array('payment_method pm', 'a.payment_method_id = pm.id', 'INNER'),
                    array('user u', 'u.id = a.validation_key_1', 'LEFT'),
                    array('employee e', 'u.employee_id = e.id', 'LEFT'),
                );
                $receipt = $this->Receipt->select($receipt_id, $select, $where, $join);

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

                    $activeWorksheet->setCellValue('K' . $coordinate, $tanggal_terima);
                    $activeWorksheet->setCellValue('L' . $coordinate, $nama_penerima);
                    $activeWorksheet->setCellValue('M' . $coordinate, $harga_box);
                    $activeWorksheet->setCellValue('N' . $coordinate, $qty_box);
                    $activeWorksheet->setCellValue('O' . $coordinate, $qty_receipt);
                    $activeWorksheet->setCellValue('P' . $coordinate, $harga_pcs);
                    $activeWorksheet->setCellValue('Q' . $coordinate, $disc);
                    $activeWorksheet->setCellValue('R' . $coordinate, $total_disc);
                    $activeWorksheet->setCellValue('S' . $coordinate, $total_harga);
                    $activeWorksheet->setCellValue('T' . $coordinate, $no_faktur);
                    $activeWorksheet->setCellValue('U' . $coordinate, $tgl_faktur);
                    $activeWorksheet->setCellValue('V' . $coordinate, $top);
                    $activeWorksheet->setCellValue('W' . $coordinate, $tgl_jatuh_tempo);
                }                
            }

            $activeWorksheet->setCellValue('A' . $coordinate, $nama_obat);
            $activeWorksheet->setCellValue('B' . $coordinate, $qty_po);
            $activeWorksheet->setCellValue('C' . $coordinate, $satuan);
            $activeWorksheet->setCellValue('D' . $coordinate, $harga_pcs_po);
            $activeWorksheet->setCellValue('E' . $coordinate, $disc_po);
            $activeWorksheet->setCellValue('F' . $coordinate, $total_disc_po);
            $activeWorksheet->setCellValue('G' . $coordinate, $total_harga_po);
            $activeWorksheet->setCellValue('H' . $coordinate, $distributor);
            $activeWorksheet->setCellValue('I' . $coordinate, $tgl_po);
        }

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'PEMESANAN_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30);     
    }

    public function view_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 0); 
        
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
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->Purchase_order->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->Purchase_order->offset = $offset; 
            unset($where['offset']);
        }

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';

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
        $purchase_order = $this->Purchase_order->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($purchase_order); $i++) { 
            $purchase_order_id = $purchase_order[$i]->id;
            $pharmacy_catalog_id = $purchase_order[$i]->pharmacy_catalog_id;

            $nama_obat = $purchase_order[$i]->nama_obat;
            $qty_po = $purchase_order[$i]->qty;
            $satuan = $purchase_order[$i]->satuan;
            $harga_pcs_po = $purchase_order[$i]->harga_pcs;
            $disc_po = $purchase_order[$i]->disc;
            $total_disc_po = $purchase_order[$i]->total_disc;
            $total_harga_po = (float) $harga_pcs_po * (float) $qty_po - (float) $total_disc_po;
            $distributor = $purchase_order[$i]->distributor;
            $tgl_po = $purchase_order[$i]->tanggal_po;

            $select = 'a.id, a.receipt_id, a.total_po, a.total_receipt';
            $where = array(
                'purchase_order_id' => $purchase_order_id,
                'pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $join = array();
            $receipt_po_comparison = $this->Receipt_po_comparison->select(0, $select, $where, $join);
            $where = array();

            $coordinate = $i + 2;

            if (count($receipt_po_comparison) > 0) {
                $receipt_id = $receipt_po_comparison[0]->receipt_id;
                $total_po = $receipt_po_comparison[0]->total_po;
                $total_receipt = $receipt_po_comparison[0]->total_receipt;

                $keterangan = '';

                if ((int) $total_po == (int) $total_receipt) {
                    $keterangan = 'Sukses';
                }

                $activeWorksheet->setCellValue('J' . $coordinate, $keterangan);

                $select = 'a.date_receipt as tanggal_terima, 
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
                $where = array(
                    'a.is_deleted' => 0,
                    'ri.pharmacy_catalog_id' => $pharmacy_catalog_id,
                );
                $join = array(
                    array('receipt_items ri', 'a.id = ri.receipt_id', 'INNER'),
                    array('payment_method pm', 'a.payment_method_id = pm.id', 'INNER'),
                    array('user u', 'u.id = a.validation_key_1', 'LEFT'),
                    array('employee e', 'u.employee_id = e.id', 'LEFT'),
                );
                $receipt = $this->Receipt->select($receipt_id, $select, $where, $join);
                $join = array();
                $where = array();
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
                    $activeWorksheet->setCellValue('A' . $coordinate, $nama_obat);
                    $activeWorksheet->setCellValue('B' . $coordinate, $qty_po);
                    $activeWorksheet->setCellValue('C' . $coordinate, $satuan);
                    $activeWorksheet->setCellValue('D' . $coordinate, $harga_pcs_po);
                    $activeWorksheet->setCellValue('E' . $coordinate, $disc_po);
                    $activeWorksheet->setCellValue('F' . $coordinate, $total_disc_po);
                    $activeWorksheet->setCellValue('G' . $coordinate, $total_harga_po);
                    $activeWorksheet->setCellValue('H' . $coordinate, $distributor);
                    $activeWorksheet->setCellValue('I' . $coordinate, $tgl_po);
                    $activeWorksheet->setCellValue('K' . $coordinate, $tanggal_terima);
                    $activeWorksheet->setCellValue('L' . $coordinate, $nama_penerima);
                    $activeWorksheet->setCellValue('M' . $coordinate, $harga_box);
                    $activeWorksheet->setCellValue('N' . $coordinate, $qty_box);
                    $activeWorksheet->setCellValue('O' . $coordinate, $qty_receipt);
                    $activeWorksheet->setCellValue('P' . $coordinate, $harga_pcs);
                    $activeWorksheet->setCellValue('Q' . $coordinate, $disc);
                    $activeWorksheet->setCellValue('R' . $coordinate, $total_disc);
                    $activeWorksheet->setCellValue('S' . $coordinate, $total_harga);
                    $activeWorksheet->setCellValue('T' . $coordinate, $no_faktur);
                    $activeWorksheet->setCellValue('U' . $coordinate, $tgl_faktur);
                    $activeWorksheet->setCellValue('V' . $coordinate, $top);
                    $activeWorksheet->setCellValue('W' . $coordinate, $tgl_jatuh_tempo);
                }                
            }
        }
        $this->Purchase_order->limit = null;
        $this->Purchase_order->offset = null;
        $select = 'count(*) as iTotal';
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
        $iTotal = $this->Purchase_order->select(0, $select, $where, $join, $order)[0]->iTotal;
        $response_data = (object) array(
			'rows' => $purchase_order,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
                
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}