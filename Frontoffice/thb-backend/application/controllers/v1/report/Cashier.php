<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Cashier extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/finance/Cashier_model", "Cashier");
        $this->load->model("v1/finance/Payment_model", "Payment");
        // Load Helper
        $this->load->helper("excel");
    }

    public function daily_all_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // } 

        $by = 'how_to_pay';

        if (isset($q_strings['by'])) {
            $by = $q_strings['by'];
        }

        $date_start = date('Y-m-d') . ' 00:00:00';
        $date_end = date('Y-m-d') . ' 23:59:59';

        if (isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
        }

        $rows = array();

        $select = 'hf.name AS instalasi, hfi.name AS poli, pm.name AS cara_bayar, a.date_added AS tanggal, h2.name as how_to_pay, c.name as insurance_name, r.norm, pn.name AS nama_pasien, e.fullname AS nama_dokter, ce.fullname AS cashier_name, ti.code, ti.var_3 AS billing, pi.var_3 AS nominal_bayar';
        $join = array(
            array('payment_item pi', 'a.id = pi.payment_id', 'LEFT'),
            array('payment_method pm', 'pi.payment_method_id = pm.id', 'LEFT'),
            array('transaction_invoice ti', 'a.invoice_id = ti.id', 'LEFT'),
            array('registration r', 'ti.registration_id = r.id', 'INNER'),
            array('registration_destination rd', 'r.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id', 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('user u', 'a.cashier_id = u.id', 'LEFT'),
            array('employee ce', 'u.employee_id = ce.id', 'LEFT'),
            array('patient pn', 'r.norm = pn.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
        );
        $where = array(
            'ri.insurance_id' => 1,
            'ri.is_primary' => 1,
            'a.is_delete' => 0,
            'ti.is_delete' => 0,
            'a.date_added >=' => $date_start,
            'a.date_added <=' => $date_end,
        );

        if ($by == 'how_to_pay') {
            $order = array(
                'cara_bayar' => 'ASC',
                'tanggal' => 'ASC',
            );
        } elseif ($by == 'health_facility_item') {
            $order = array(
                'poli' => 'ASC',
                'tanggal' => 'ASC',
            );
        } elseif ($by == 'doctor') {
            $order = array(
                'nama_dokter' => 'ASC',
                'tanggal' => 'ASC',
            );
        }
        $group = 'rd.registration_id';

        $report = $this->Payment->select(0, $select, $where, $join, $order, $group);

        $rows_pb = array();

        $select_pb = 'pm.name AS cara_bayar, a.date_added AS tanggal, r.fullname AS nama_pasien, ce.fullname AS cashier_name, ti.code, ti.var_3 AS billing, pi.var_3 AS nominal_bayar';
        $join_pb = array(
            array('payment_item pi', 'a.id = pi.payment_id', 'LEFT'),
            array('payment_method pm', 'pi.payment_method_id = pm.id', 'LEFT'),
            array('transaction_invoice ti', 'a.invoice_id = ti.id', 'LEFT'),
            array('pharmacy_prescription r', 'ti.pharmacy_prescription_id = r.id', 'INNER'),
            array('user u', 'a.cashier_id = u.id', 'LEFT'),
            array('employee ce', 'u.employee_id = ce.id', 'LEFT'),
        );
        $where_pb = array(
            'a.is_delete' => 0,
            'ti.is_delete' => 0,
            'a.date_added >=' => $date_start,
            'a.date_added <=' => $date_end,
        );

        $order_pb = array(
            'cara_bayar' => 'ASC',
            'tanggal' => 'ASC',
        );
        

        $report_pb = $this->Payment->select(0, $select_pb, $where_pb, $join_pb, $order_pb);

        $merged = [];

        foreach ($report as $r) {
            $key = $r->code ?? null;
            if ($key) {
                $merged[$key] = $r;
            }
        }

        foreach ($report_pb as $pb) {
            $key = $pb->code ?? null;
            if ($key) {
                if (!isset($merged[$key])) {
                    $merged[$key] = $pb;
                } else {
                    // Gabungkan datanya jika key sama
                    $merged[$key] = (object) array_merge((array) $merged[$key], (array) $pb);
                }
            }
        }

        $combined_report = array_values($merged);
        
        $this->REST_Return(201, 'SUCCESS', $combined_report);
        return;
    }

    // public function daily_all_get() {
    //     header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
        
    //     // Token Validation
    //     // $is_valid_token = $this->authorization_token->validateToken();
    //     // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
    //     //     $response_data = 'Invalid Token.';
    //     //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
    //     //     return;
    //     // } 

    //     $by = 'how_to_pay';

    //     if (isset($q_strings['by'])) {
    //         $by = $q_strings['by'];
    //     }

    //     $date_start = date('Y-m-d') . ' 00:00:00';
    //     $date_end = date('Y-m-d') . ' 23:59:59';

    //     if (isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
    //         $date_start = $q_strings['date_start'];
    //         $date_end = $q_strings['date_end'];
    //     }

    //     $style = array(
    //         'borders' => array(
    //             'allBorders' => array(
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //                 'color' => array(
    //                     'argb' => 'FF000000',
    //                 ),
    //             ),
    //         ),
    //     );

    //     $headers = array(
    //         'Tanggal' => array(
    //             'width' => 19
    //         ),
    //         'No. Invoice' => array(
    //             'width' => 10
    //         ),
    //         'No. RM' => array(
    //             'width' => 10
    //         ),
    //         'Nama Pasien' => array(
    //             'width' => 40
    //         ),
    //         'Keterangan' => array(
    //             'width' => 40
    //         ),
    //         'Billing' => array(
    //             'width' => 15
    //         ),
    //         'Cara Bayar' => array(
    //             'width' => 15
    //         ),
    //         'Nominal Bayar' => array(
    //             'width' => 15
    //         ),
    //     );

    //     $rows = array();

    //     $select = 'hf.name AS instalasi, hfi.name AS poli, pm.name AS cara_bayar, a.date_added AS tanggal, r.norm, pn.name AS nama_pasien, e.fullname AS nama_dokter, ti.code, ti.var_3 AS billing, pi.var_3 AS nominal_bayar';
    //     $join = array(
    //         array('payment_item pi', 'a.id = pi.payment_id', 'INNER'),
    //         array('payment_method pm', 'pi.payment_method_id = pm.id', 'INNER'),
    //         array('transaction_invoice ti', 'a.invoice_id = ti.id', 'INNER'),
    //         array('registration r', 'ti.registration_id = r.id', 'INNER'),
    //         array('registration_destination rd', 'r.id = rd.registration_id', 'INNER'),
    //         array('registration_insurance ri', 'rd.id = ri.registration_destination_id', 'INNER'),
    //         array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
    //         array('patient pn', 'r.norm = pn.norm', 'INNER'),
    //         array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
    //         array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
    //     );
    //     $where = array(
    //         'ri.insurance_id' => 1,
    //         'ri.is_primary' => 1,
    //         'a.is_delete' => 0,
    //         'ti.is_delete' => 0,
    //         'a.date_added >=' => $date_start,
    //         'a.date_added <=' => $date_end,
    //     );

    //     if ($by == 'how_to_pay') {
    //         $order = array(
    //             'cara_bayar' => 'ASC',
    //             'tanggal' => 'ASC',
    //         );
    //     } elseif ($by == 'health_facility_item') {
    //         $order = array(
    //             'poli' => 'ASC',
    //             'tanggal' => 'ASC',
    //         );
    //     } elseif ($by == 'doctor') {
    //         $order = array(
    //             'nama_dokter' => 'ASC',
    //             'tanggal' => 'ASC',
    //         );
    //     }

    //     $report = $this->Payment->select(0, $select, $where, $join, $order);

    //     $now = '';
    //     $n = 0;
    //     for ($i=0; $i < count($report); $i++) { 
    //         $no_invoice = $report[$i]->code;
    //         $poli = $report[$i]->poli;
    //         $instalasi = $report[$i]->instalasi;
    //         $cara_bayar = $report[$i]->cara_bayar;
    //         $tanggal = $report[$i]->tanggal;
    //         $norm = $report[$i]->norm;
    //         $nama_pasien = $report[$i]->nama_pasien;
    //         $nama_dokter = $report[$i]->nama_dokter;
    //         $billing = $report[$i]->billing;
    //         $nominal_bayar = $report[$i]->nominal_bayar;

    //         $row = array($tanggal, $no_invoice, $norm, $nama_pasien, $nama_dokter . ' - ' . $instalasi, $billing,$cara_bayar, $nominal_bayar);
            
    //         if ($by == 'how_to_pay') {
    //             $head = $cara_bayar;
    //         } elseif ($by == 'health_facility_item') {
    //             $head = $poli;
    //         } elseif ($by == 'doctor') {
    //             $head = $nama_dokter;
    //             $row = array($tanggal, $no_invoice, $norm, $nama_pasien, $instalasi . ' - ' . $poli, $billing,$cara_bayar, $nominal_bayar);
    //         }

    //         if ($now != $head) {
    //             $n = 0;
    //         }
            
    //         if ($n == 0) {
    //             array_push($rows, $head);
    //             $now = $head;
    //             $n += 1;
    //         } 
            
    //         array_push($rows, $row);
    //     }

    //     $spreadsheet = create_excel($headers, $rows, $style);
        
    //     $writer = new Xlsx($spreadsheet);

    //     $filename = 'Report_Cashier_' . date('Ymd_His');

    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
    //     header('Cache-Control: max-age=0');
    //     $writer->save('php://output');
    // }

    public function daily_cashier_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $cashier_id = $id;

        if ($cashier_id == 0) {
            $this->REST_Return(400, 'cashier id is required');
            return;
        }

        $select = 'a.id';
        $cashier = $this->Cashier->select($cashier_id, $select);
        
        if (count($cashier) == 0) {
            $this->REST_Return(404, 'there is no cashier with id ' . $cashier_id);
            return;
        }

        $date_start = date('Y-m-d') . ' 00:00:00';
        $date_end = date('Y-m-d') . ' 23:59:59';

        if (isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'];
            $date_end = $q_strings['date_end'];
        }

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
            'NOMOR NOTA' => array(
                'width' => 25
            ),
            'NAMA PASIEN' => array(
                'width' => 40
            ),
            'NO RM' => array(
                'width' => 10
            ),
            'JENIS BAYAR' => array(
                'width' => 15
            ),
            'CASH' => array(
                'width' => 15
            ),
            'MANDIRI' => array(
                'width' => 15
            ),
            'BSI' => array(
                'width' => 15
            ),
            'BNI' => array(
                'width' => 15
            ),
            'BCA' => array(
                'width' => 15
            ),
            'DEPOSIT MASUK' => array(
                'width' => 20
            ),

        );

        $rows = array();
        
        $report = $this->Cashier->daily_cashier($date_start, $date_end, $cashier_id);

        for ($i=0; $i < count($report); $i++) { 
            $nomor_nota = $report[$i]->nomor_nota;
            $nama_pasien = $report[$i]->nama_pasien;
            $norm = $report[$i]->norm;
            $jenis_bayar = $report[$i]->jenis_bayar;
            $cara_bayar = $report[$i]->cara_bayar;
            $deposit_masuk = $report[$i]->deposit_masuk;
            $nominal_bayar = $report[$i]->nominal_bayar;

            $cash = '';
            $mandiri = '';
            $bsi = '';
            $bni = '';
            $bca = '';

            if ($cara_bayar == 'BANK BCA') {
                $bca = $nominal_bayar;
            } elseif ($cara_bayar == 'BANK BSI') {
                $bsi = $nominal_bayar;
            } elseif ($cara_bayar == 'BANK BNI') {
                $bni = $nominal_bayar;
            } elseif ($cara_bayar == 'BANK MANDIRI') {
                $mandiri = $nominal_bayar;
            } elseif ($cara_bayar == 'CASH/TUNAI') {
                $cash = $nominal_bayar;
            } 

            $row = array($nomor_nota, $nama_pasien, $norm, $jenis_bayar, $cash, $mandiri, $bsi, $bni, $bca, $deposit_masuk);
            
            array_push($rows, $row);
        }

        $spreadsheet = create_excel($headers, $rows, $style);
        
        $writer = new Xlsx($spreadsheet);

        $filename = 'Report_Cashier2_' . date('Ymd_His');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

    public function daily_shift_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        if ($q_strings['date'] == null) {
            $this->REST_Return(400, 'field date is required!');
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
            'NO' => array(
                'width' => 6
            ),
            'URAIAN LENGKAP' => array(
                'width' => 55
            ),
            'REKENING' => array(
                'width' => 22
            ),
            'PENERIMAAN' => array(
                'width' => 22
            ),
            'PENGELUARAN' => array(
                'width' => 22
            ),
        );

        
        $report = $this->Cashier->daily_shift($date);
        
        $rows = array(
            array('1', 'CASH SHIFT MALAM (00:00 S/D 07:00) TGL ' . date('d/m/Y'), '', ($report[3] != 0 ? $report[3] : '-'), '-'),
            array('2', 'CASH SHIFT PAGI (07:00 S/D 14:00) TGL ' . date('d/m/Y'), '', ($report[0] != 0 ? $report[0] : '-'), '-'),
            array('3', 'CASH SHIFT SIANG (14:00 S/D 21:00) TGL ' . date('d/m/Y'), '', ($report[1] != 0 ? $report[1] : '-'), '-'),
            array('4', 'CASH SHIFT MALAM (21:00 S/D 00:00) TGL ' . date('d/m/Y'), '', ($report[2] != 0 ? $report[2] : '-'), '-'),
        );

        $spreadsheet = create_excel($headers, $rows, $style);
        
        $writer = new Xlsx($spreadsheet);

        $filename = 'Report_Cashier_' . date('Ymd_His');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}