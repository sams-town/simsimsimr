<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Selling extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/pharmacy/Prescription_model", "Prescription");
        $this->load->model("v1/pharmacy/Receipt_model_item", "Receipt_item");
        $this->load->model("v1/mr/Patient_model", "Patient");
        // Load Helper
        $this->load->helper("excel");
        $this->load->helper('logger');
    }

    public function get_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
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
            'No. Resep' => array(
                'width' => 15
            ),
            'Tanggal Resep' => array(
                'width' => 15
            ),
            'type_prescription_id' => array(
                'width' => 15
            ),
            'date_0' => array(
                'width' => 15
            ),
            'tgl_pulang' => array(
                'width' => 15
            ),
            'Cara Bayar' => array(
                'width' => 15
            ),
            'Nama Penjamin' => array(
                'width' => 15
            ),
            'Instalasi' => array(
                'width' => 15
            ),
            'No. RM' => array(
                'width' => 15
            ),
            'Nama Pasien' => array(
                'width' => 15
            ),
            'Nama DPJP' => array(
                'width' => 15
            ),
            'Nama Dokter' => array(
                'width' => 15
            ),
            'Poliklinik' => array(
                'width' => 15
            ),
            'Depo' => array(
                'width' => 15
            ),
            'Catalog' => array(
                'width' => 15
            ),
            'Jumlah' => array(
                'width' => 15
            ),
            'Jumlah Retur' => array(
                'width' => 15
            ),
            'Harga' => array(
                'width' => 15
            ),
            'Satuan Jual' => array(
                'width' => 15
            ),
            'Total Harga' => array(
                'width' => 15
            ),
            'HPP' => array(
                'width' => 15
            ),
            'Satuan Beli' => array(
                'width' => 15
            ),
            'Total HPP' => array(
                'width' => 15
            ),
        );

        // $select = "a.no_order, 
        // a.date_added, 
        // a.type_prescription_id, 
        // a.date_0, 
        // a.date_1, 
        // h.name as how_to_pay, 
        // c.name as insurance_name, 
        // h2.name as how_to_pay2, 
        // c2.name as insurance_name2, 
        // hf.name as health_facility_name, 
        // a.norm, 
        // e.fullname as doctor_name, 
        // dpjp.fullname as dpjp_name, 
        // dpjp2.fullname as dpjp_name2, 
        // hfi.name as health_facility_name, 
        // pd.name as depo_name, 
        // pc.name as catalog_name, 
        // pi.package_name, 
        // pi.pharmacy_catalog_id, 
        // pi.pharmacy_catalog_id_ref, 
        // pi.qty, 
        // pi.qty_retur, 
        // pi.var_1
        // ";
        // $where = array(
        //     'a.is_delete' => 0,
        //     'a.flag' => 1,
        //     'pi.is_delete' => 0,
        //     'a.date_added >=' => $date_start,
        //     'a.date_added <=' => $date_end,
        // );

        // if ($q_strings['health_facility_id'] != null) {
        //     $where['hf.id'] = $q_strings['health_facility_id'];
        // }

        // $join = array(
        //     array('pharmacy_prescription_item pi', 'a.id = pi.pharmacy_prescription_id', 'INNER'),
        //     array('pharmacy_catalog pc', 'pi.pharmacy_catalog_id = pc.id', 'INNER'),
        //     array('package pkg', 'pc.package_id = pkg.id', 'INNER'),
        //     array('pharmacy_depo pd', 'a.pharmacy_depo_id = pd.id', 'INNER'),
        //     array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
        //     array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
        //     array('health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'),
        //     // array('patient ptn', 'a.norm = ptn.norm', 'INNER'),
        //     array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
        //     array('employee dpjp', 'rd.doctor_in_charge_id = dpjp.id', 'LEFT'),
        //     array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary = 1', 'LEFT'),
        //     array('helper h', 'ri.insurance_id = h.code AND h.variable = "insurance"', 'LEFT'),
        //     array('company c', 'ri.company_id = c.id', 'LEFT'),
        //     array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
        //     array('soap s', 'sp.soap_id = s.id', 'LEFT'),
        //     array('registration_destination rd2', 's.registration_destination_id = rd2.id', 'LEFT'),
        //     array('registration_insurance ri2', 'rd2.id = ri2.registration_destination_id AND ri2.is_primary = 1', 'LEFT'),
        //     array('helper h2', 'ri2.insurance_id = h2.code AND h2.variable = "insurance"', 'LEFT'),
        //     array('company c2', 'ri2.company_id = c2.id', 'LEFT'),
        //     array('employee dpjp2', 'rd2.doctor_in_charge_id = dpjp2.id', 'LEFT'),
        // );
        // $order = array('a.date_added' => 'ASC');
        // $this->db->group_by(array("pi.pharmacy_prescription_id", "pi.pharmacy_catalog_id"));

        $select = "
            a.no_order,
            a.date_added,
            a.type_prescription_id,
            a.date_0,
            a.date_1,
            h.name AS how_to_pay,
            c.name AS insurance_name,
            h2.name AS how_to_pay2,
            c2.name AS insurance_name2,
            hf.name AS health_facility_name,
            a.norm,
            rd.checkout_date,
            e.fullname AS doctor_name,
            dpjp.fullname AS dpjp_name,
            dpjp2.fullname AS dpjp_name2,
            hfi.name AS health_facility_name_item,
            pd.name AS depo_name,
            pc.name AS catalog_name,
            pi.package_name,
            pi.pharmacy_catalog_id,
            pi.pharmacy_catalog_id_ref,
            pi.qty,
            pi.qty_retur,
            pi.var_1
        ";

        $join = [
            ['pharmacy_prescription_item pi', 'a.id = pi.pharmacy_prescription_id', 'INNER'],
            ['pharmacy_catalog pc', 'pi.pharmacy_catalog_id = pc.id', 'INNER'],
            ['package pkg', 'pc.package_id = pkg.id', 'INNER'],
            ['pharmacy_depo pd', 'a.pharmacy_depo_id = pd.id', 'INNER'],
            ['employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'],
            ['health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'],
            ['health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'],
            ['registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'],
            ['employee dpjp', 'rd.doctor_in_charge_id = dpjp.id', 'LEFT'],
            ['registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary = 1', 'LEFT'],
            ['helper h', 'ri.insurance_id = h.code AND h.variable = "insurance"', 'LEFT'],
            ['company c', 'ri.company_id = c.id', 'LEFT'],
            ['soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'],
            ['soap s', 'sp.soap_id = s.id', 'LEFT'],
            ['registration_destination rd2', 's.registration_destination_id = rd2.id', 'LEFT'],
            ['registration_insurance ri2', 'rd2.id = ri2.registration_destination_id AND ri2.is_primary = 1', 'LEFT'],
            ['helper h2', 'ri2.insurance_id = h2.code AND h2.variable = "insurance"', 'LEFT'],
            ['company c2', 'ri2.company_id = c2.id', 'LEFT'],
            ['employee dpjp2', 'rd2.doctor_in_charge_id = dpjp2.id', 'LEFT'],
        ];

        $where = [
            'a.is_delete' => 0,
            'a.flag' => 1,
            'pi.is_delete' => 0,
        ];

        if (!empty($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
        }

        $order = ['a.date_added' => 'ASC'];

        // Build query
        $this->db
            ->select($select)
            ->from('pharmacy_prescription a');

        foreach ($join as $j) {
            $this->db->join($j[0], $j[1], $j[2]);
        }

        $this->db->where('a.is_delete', 0);
        $this->db->where('a.flag', 1);
        $this->db->where('pi.is_delete', 0);
        if (!empty($q_strings['health_facility_id'])) {
            $this->db->where('hf.id', $q_strings['health_facility_id']);
        }

        // bentuk: (rd.checkout_date BETWEEN ... ) OR (rd.id IS NULL AND a.date_added BETWEEN ...)
        $this->db->group_start();
            $this->db->group_start();
                $this->db->where('rd.checkout_date >=', $date_start);
                $this->db->where('rd.checkout_date <=', $date_end);
            $this->db->group_end();
            $this->db->or_group_start();
                $this->db->where('rd.id IS NULL'); // resep tanpa registration_destination
                $this->db->where('a.date_added >=', $date_start);
                $this->db->where('a.date_added <=', $date_end);
            $this->db->group_end();
        $this->db->group_end();

        $this->db->group_by(['pi.pharmacy_prescription_id','pi.pharmacy_catalog_id']);
        $this->db->order_by('a.date_added','ASC');

        $query = $this->db->get();
        $prescription = $query->result();

        // $prescription = $this->Prescription->select(0, $select, $where, $join, $order);

        $norms = [];
        for ($i=0; $i < count($prescription); $i++) { 
            $norm = $prescription[$i]->norm;
            if ($norm != null) {
                $norms[] = $norm;
            }
        }

        $param = implode(',',$norms);

        $patient = $this->db->query("
            SELECT norm, fullname
            FROM patient
            WHERE norm in ($param)
        ")->result();

        $dt_patient = [];
        for ($i=0; $i < count($patient); $i++) { 
            $dt_patient[$patient[$i]->norm] = array(
                'fullname' => $patient[$i]->fullname,
            );
        }
        
        Logger::info($this->debug_uniq, 'total rows: ' . count($prescription));

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

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

        $count = 0;
        for ($i=0; $i < count($prescription); $i++) { 

            // $select_receipt = "a.price_item,a.pharmacy_catalog_id,a.discount_item,a.package_name as package_name_receipt" ;
            // $join_receipt = array(
            //     array('pharmacy_assets_price pap', 'a.pharmacy_catalog_id = pap.pharmacy_catalog_id AND a.batch = pap.batch', 'INNER'),
            // );
            // $where_receipt = array(
            //     'a.pharmacy_catalog_id' => $prescription[$i]->pharmacy_catalog_id,
            // );

            // $group_receipt = array('a.batch');
            // $order_receipt = array('a.discount_item' => 'DESC');

            // $receipt = $this->Receipt_item->select(0, $select_receipt, $where_receipt, $join_receipt, $order_receipt, $group_receipt)[0];

            $receipt = $this->db->query("SELECT 
                    (
                        SELECT MIN(pap2.hna_item)
                        FROM pharmacy_assets_price pap2
                        WHERE pap2.pharmacy_catalog_id = a.pharmacy_catalog_id
                        AND pap2.batch = a.batch
                    ) AS price_item,
                    a.pharmacy_catalog_id, 
                    a.discount_item, 
                    a.package_name AS package_name_receipt
                FROM receipt_items AS a
                INNER JOIN pharmacy_assets_price pap 
                    ON a.pharmacy_catalog_id = pap.pharmacy_catalog_id 
                    AND a.batch = pap.batch
                WHERE a.pharmacy_catalog_id = '{$prescription[$i]->pharmacy_catalog_id}'
                ORDER BY 
                    price_item ASC,
                    a.discount_item DESC
                LIMIT 1")->result()[0];

            
            $count += 1;

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

            $no_order = $prescription[$i]->no_order;
            $date_added = $prescription[$i]->date_added;
            $type_prescription_id = $prescription[$i]->type_prescription_id;
            $pharmacy_catalog_id_ref = $prescription[$i]->pharmacy_catalog_id_ref;
            $date_0 = $prescription[$i]->date_0;
            $date_1 = $prescription[$i]->checkout_date;
            $how_to_pay = $prescription[$i]->how_to_pay;
            $insurance_name = $prescription[$i]->insurance_name;
            $how_to_pay2 = $prescription[$i]->how_to_pay2;
            $insurance_name2 = $prescription[$i]->insurance_name2;
            $health_facility_name = $prescription[$i]->health_facility_name;
            $norm = $prescription[$i]->norm;
            $patient_name = '-';
            $doctor_name = $prescription[$i]->doctor_name;
            $dpjp_name = $prescription[$i]->dpjp_name;
            $dpjp_name2 = $prescription[$i]->dpjp_name2;
            $health_facility_name = $prescription[$i]->health_facility_name;
            $depo_name = $prescription[$i]->depo_name;
            $catalog_name = $prescription[$i]->catalog_name;
            $package_name = $prescription[$i]->package_name;
            $qty = (float) $prescription[$i]->qty;
            $qty_retur = (float) $prescription[$i]->qty_retur;
            $var_1 = (float) $prescription[$i]->var_1;
            $hna = ($var_1/1.3);
            if ($receipt->pharmacy_catalog_id == $prescription[$i]->pharmacy_catalog_id) {
                $hpp = (float) ($receipt->price_item-(($receipt->price_item*$receipt->discount_item)/100));
                $package_name_receipt = $receipt->package_name_receipt;
            }

            $patient_name = $dt_patient[$norm]['fullname'];
            
            $total = $var_1 * $qty;

            if ($total > 0) {
                if ($hpp > $hna) {
                    $hpp = $hna;
                }
                $total_hpp = $hpp * $qty;
            } else {
                $hpp = 0;
                $total_hpp = 0;
            }

            if ($pharmacy_catalog_id_ref == null) {
                if ($qty_retur != null) {
                    $total = ($qty - $qty_retur) * $var_1;
                    if ($total > 0) {
                        $total_hpp = ($qty - $qty_retur) * $hpp;
                    } else {
                        $hpp = 0;
                        $total_hpp = 0;
                    }
                }
            }

            $coordinate = $i + 2;
            $activeWorksheet->setCellValue('A' . $coordinate, $no_order);
            $activeWorksheet->setCellValue('B' . $coordinate, $date_added);
            $activeWorksheet->setCellValue('C' . $coordinate, $type_prescription_id);
            $activeWorksheet->setCellValue('D' . $coordinate, $date_0);
            $activeWorksheet->setCellValue('E' . $coordinate, $date_1);
            $activeWorksheet->setCellValue('F' . $coordinate, ($how_to_pay != null) ? $how_to_pay : $how_to_pay2);
            $activeWorksheet->setCellValue('G' . $coordinate, $type_prescription_id == 5 ? 'Tunai/Pribadi' : ($insurance_name != null) ? $insurance_name : $insurance_name2);
            $activeWorksheet->setCellValue('H' . $coordinate, $health_facility_name);
            $activeWorksheet->setCellValue('I' . $coordinate, $norm);
            $activeWorksheet->setCellValue('J' . $coordinate, $patient_name);
            $activeWorksheet->setCellValue('K' . $coordinate, ($dpjp_name != null) ? $dpjp_name : $dpjp_name2);
            $activeWorksheet->setCellValue('L' . $coordinate, $doctor_name);
            $activeWorksheet->setCellValue('M' . $coordinate, $health_facility_name);
            $activeWorksheet->setCellValue('N' . $coordinate, $depo_name);
            $activeWorksheet->setCellValue('O' . $coordinate, $catalog_name);
            $activeWorksheet->setCellValue('P' . $coordinate, $qty);
            $activeWorksheet->setCellValue('Q' . $coordinate, $pharmacy_catalog_id_ref == null ? $qty_retur : 0);
            $activeWorksheet->setCellValue('R' . $coordinate, $var_1);
            $activeWorksheet->setCellValue('S' . $coordinate, $package_name);
            $activeWorksheet->setCellValue('T' . $coordinate, $total);
            $activeWorksheet->setCellValue('U' . $coordinate, $hpp);
            $activeWorksheet->setCellValue('V' . $coordinate, $package_name_receipt);
            $activeWorksheet->setCellValue('W' . $coordinate, $total_hpp);

            if ($count % 1000 == 0) {
                Logger::info($this->debug_uniq, 'current processed rows: ' . $count);
            }
        }

        $writer = new Xlsx($spreadsheet);
        
        $filename = $this->name . '_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        Logger::info($this->debug_uniq, "|---- finish export selling prescription ----|");
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $formattedTime = number_format($executionTime, 3, '.', '');
        Logger::info($this->debug_uniq, "execution time: " . $formattedTime . " seconds");

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function get_hpp_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
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
            'No. Resep' => array(
                'width' => 15
            ),
            'Tanggal Resep' => array(
                'width' => 15
            ),
            'type_prescription_id' => array(
                'width' => 15
            ),
            'date_0' => array(
                'width' => 15
            ),
            'tgl_pulang' => array(
                'width' => 15
            ),
            'Cara Bayar' => array(
                'width' => 15
            ),
            'Nama Penjamin' => array(
                'width' => 15
            ),
            'Instalasi' => array(
                'width' => 15
            ),
            'No. RM' => array(
                'width' => 15
            ),
            'Nama Pasien' => array(
                'width' => 15
            ),
            'Nama DPJP' => array(
                'width' => 15
            ),
            'Nama Dokter' => array(
                'width' => 15
            ),
            'Poliklinik' => array(
                'width' => 15
            ),
            'Depo' => array(
                'width' => 15
            ),
            'Catalog' => array(
                'width' => 15
            ),
            'Jumlah' => array(
                'width' => 15
            ),
            'Jumlah Retur' => array(
                'width' => 15
            ),
            'Harga' => array(
                'width' => 15
            ),
            'Satuan Jual' => array(
                'width' => 15
            ),
            'Total Harga' => array(
                'width' => 15
            ),
            'HPP' => array(
                'width' => 15
            ),
            'Satuan Beli' => array(
                'width' => 15
            ),
            'Total HPP' => array(
                'width' => 15
            ),
        );

        // $select = "a.no_order, 
        // a.date_added, 
        // a.type_prescription_id, 
        // a.date_0, 
        // a.date_1, 
        // h.name as how_to_pay, 
        // c.name as insurance_name, 
        // h2.name as how_to_pay2, 
        // c2.name as insurance_name2, 
        // hf.name as health_facility_name, 
        // a.norm, 
        // e.fullname as doctor_name, 
        // dpjp.fullname as dpjp_name, 
        // dpjp2.fullname as dpjp_name2, 
        // hfi.name as health_facility_name, 
        // pd.name as depo_name, 
        // pc.name as catalog_name, 
        // pi.package_name, 
        // pi.pharmacy_catalog_id, 
        // pi.pharmacy_catalog_id_ref, 
        // pi.qty, 
        // pi.qty_retur, 
        // pi.var_1
        // ";
        // $where = array(
        //     'a.is_delete' => 0,
        //     'a.flag' => 1,
        //     'pi.is_delete' => 0,
        //     'a.date_added >=' => $date_start,
        //     'a.date_added <=' => $date_end,
        // );

        // if ($q_strings['health_facility_id'] != null) {
        //     $where['hf.id'] = $q_strings['health_facility_id'];
        // }

        // $join = array(
        //     array('pharmacy_prescription_item pi', 'a.id = pi.pharmacy_prescription_id', 'INNER'),
        //     array('pharmacy_catalog pc', 'pi.pharmacy_catalog_id = pc.id', 'INNER'),
        //     array('package pkg', 'pc.package_id = pkg.id', 'INNER'),
        //     array('pharmacy_depo pd', 'a.pharmacy_depo_id = pd.id', 'INNER'),
        //     array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
        //     array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
        //     array('health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'),
        //     // array('patient ptn', 'a.norm = ptn.norm', 'INNER'),
        //     array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
        //     array('employee dpjp', 'rd.doctor_in_charge_id = dpjp.id', 'LEFT'),
        //     array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary = 1', 'LEFT'),
        //     array('helper h', 'ri.insurance_id = h.code AND h.variable = "insurance"', 'LEFT'),
        //     array('company c', 'ri.company_id = c.id', 'LEFT'),
        //     array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
        //     array('soap s', 'sp.soap_id = s.id', 'LEFT'),
        //     array('registration_destination rd2', 's.registration_destination_id = rd2.id', 'LEFT'),
        //     array('registration_insurance ri2', 'rd2.id = ri2.registration_destination_id AND ri2.is_primary = 1', 'LEFT'),
        //     array('helper h2', 'ri2.insurance_id = h2.code AND h2.variable = "insurance"', 'LEFT'),
        //     array('company c2', 'ri2.company_id = c2.id', 'LEFT'),
        //     array('employee dpjp2', 'rd2.doctor_in_charge_id = dpjp2.id', 'LEFT'),
        // );
        // $order = array('a.date_added' => 'ASC');
        // $this->db->group_by(array("pi.pharmacy_prescription_id", "pi.pharmacy_catalog_id"));

        $select = "
            a.no_order,
            a.date_added,
            a.type_prescription_id,
            a.date_0,
            a.date_1,
            h.name AS how_to_pay,
            c.name AS insurance_name,
            h2.name AS how_to_pay2,
            c2.name AS insurance_name2,
            hf.name AS health_facility_name,
            a.norm,
            rd.checkout_date,
            e.fullname AS doctor_name,
            dpjp.fullname AS dpjp_name,
            dpjp2.fullname AS dpjp_name2,
            hfi.name AS health_facility_name_item,
            pd.name AS depo_name,
            pc.name AS catalog_name,
            pi.package_name,
            pi.pharmacy_catalog_id,
            pi.pharmacy_catalog_id_ref,
            pi.qty,
            pi.qty_retur,
            pi.var_1
        ";

        $join = [
            ['pharmacy_prescription_item pi', 'a.id = pi.pharmacy_prescription_id', 'INNER'],
            ['pharmacy_catalog pc', 'pi.pharmacy_catalog_id = pc.id', 'INNER'],
            ['package pkg', 'pc.package_id = pkg.id', 'INNER'],
            ['pharmacy_depo pd', 'a.pharmacy_depo_id = pd.id', 'INNER'],
            ['employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'],
            ['health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'],
            ['health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'],
            ['registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'],
            ['employee dpjp', 'rd.doctor_in_charge_id = dpjp.id', 'LEFT'],
            ['registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary = 1', 'LEFT'],
            ['helper h', 'ri.insurance_id = h.code AND h.variable = "insurance"', 'LEFT'],
            ['company c', 'ri.company_id = c.id', 'LEFT'],
            ['soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'],
            ['soap s', 'sp.soap_id = s.id', 'LEFT'],
            ['registration_destination rd2', 's.registration_destination_id = rd2.id', 'LEFT'],
            ['registration_insurance ri2', 'rd2.id = ri2.registration_destination_id AND ri2.is_primary = 1', 'LEFT'],
            ['helper h2', 'ri2.insurance_id = h2.code AND h2.variable = "insurance"', 'LEFT'],
            ['company c2', 'ri2.company_id = c2.id', 'LEFT'],
            ['employee dpjp2', 'rd2.doctor_in_charge_id = dpjp2.id', 'LEFT'],
        ];

        $where = [
            'a.is_delete' => 0,
            'a.flag' => 1,
            'pi.is_delete' => 0,
        ];

        if (!empty($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
        }

        $order = ['a.date_added' => 'ASC'];

        // Build query
        $this->db
            ->select($select)
            ->from('pharmacy_prescription a');

        foreach ($join as $j) {
            $this->db->join($j[0], $j[1], $j[2]);
        }

        $this->db->where('a.is_delete', 0);
        $this->db->where('a.flag', 1);
        $this->db->where('pi.is_delete', 0);
        $this->db->where_not_in('pc.suppliers_id', [
            '29607783-85c9-4c80-bb09-673436abd025',
            '4164b069-15de-4e30-8ec4-40ddb4c70797',
            '4b2b43ce-ddc8-4db9-a9bc-8031c3697e4c',
            '4dbaa98b-13fb-4cd7-a65c-015a3f216e36',
            '52e313cf-ba9b-4eb7-9916-b5d2863139c0',
            '72f7d7df-17b5-4de6-b59b-576b7799723a',
            '7668d7b4-e239-4cc2-8843-c0931c0fed52',
            '7fb91255-7be4-49c7-82e5-370ff86b9b41',
            '923b4d27-2cf4-4503-8769-9922aa0ec4c6',
            '9608f20f-21cb-47d3-87ee-badf8a4a9a51',
            '98175373-29a1-4dcf-b846-cde16d54eba8',
            'ced75311-fe14-4cb2-969a-a4a8b37d6782',
            '8df35771-114f-4b84-8699-e1034fc33138'
        ]);
        if (!empty($q_strings['health_facility_id'])) {
            $this->db->where('hf.id', $q_strings['health_facility_id']);
        }

        // bentuk: (rd.checkout_date BETWEEN ... ) OR (rd.id IS NULL AND a.date_added BETWEEN ...)
        $this->db->group_start();
            $this->db->group_start();
                $this->db->where('rd.checkout_date >=', $date_start);
                $this->db->where('rd.checkout_date <=', $date_end);
            $this->db->group_end();
            $this->db->or_group_start();
                $this->db->where('rd.id IS NULL'); // resep tanpa registration_destination
                $this->db->where('a.date_added >=', $date_start);
                $this->db->where('a.date_added <=', $date_end);
            $this->db->group_end();
        $this->db->group_end();

        $this->db->group_by(['pi.pharmacy_prescription_id','pi.pharmacy_catalog_id']);
        $this->db->order_by('a.date_added','ASC');

        $query = $this->db->get();
        $prescription = $query->result();

        // $prescription = $this->Prescription->select(0, $select, $where, $join, $order);

        $norms = [];
        for ($i=0; $i < count($prescription); $i++) { 
            $norm = $prescription[$i]->norm;
            if ($norm != null) {
                $norms[] = $norm;
            }
        }

        $param = implode(',',$norms);

        $patient = $this->db->query("
            SELECT norm, fullname
            FROM patient
            WHERE norm in ($param)
        ")->result();

        $dt_patient = [];
        for ($i=0; $i < count($patient); $i++) { 
            $dt_patient[$patient[$i]->norm] = array(
                'fullname' => $patient[$i]->fullname,
            );
        }
        
        Logger::info($this->debug_uniq, 'total rows: ' . count($prescription));

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

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

        $count = 0;
        for ($i=0; $i < count($prescription); $i++) { 

            // $select_receipt = "a.price_item,a.pharmacy_catalog_id,a.discount_item,a.package_name as package_name_receipt" ;
            // $join_receipt = array(
            //     array('pharmacy_assets_price pap', 'a.pharmacy_catalog_id = pap.pharmacy_catalog_id AND a.batch = pap.batch', 'INNER'),
            // );
            // $where_receipt = array(
            //     'a.pharmacy_catalog_id' => $prescription[$i]->pharmacy_catalog_id,
            // );

            // $group_receipt = array('a.batch');
            // $order_receipt = array('a.discount_item' => 'DESC');

            // $receipt = $this->Receipt_item->select(0, $select_receipt, $where_receipt, $join_receipt, $order_receipt, $group_receipt)[0];

            $receipt = $this->db->query("SELECT 
                    (
                        SELECT MIN(pap2.hna_item)
                        FROM pharmacy_assets_price pap2
                        WHERE pap2.pharmacy_catalog_id = a.pharmacy_catalog_id
                        AND pap2.batch = a.batch
                    ) AS price_item,
                    a.pharmacy_catalog_id, 
                    a.discount_item, 
                    a.package_name AS package_name_receipt
                FROM receipt_items AS a
                INNER JOIN pharmacy_assets_price pap 
                    ON a.pharmacy_catalog_id = pap.pharmacy_catalog_id 
                    AND a.batch = pap.batch
                WHERE a.pharmacy_catalog_id = '{$prescription[$i]->pharmacy_catalog_id}'
                ORDER BY 
                    price_item ASC,
                    a.discount_item DESC
                LIMIT 1")->result()[0];

            
            $count += 1;

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

            $no_order = $prescription[$i]->no_order;
            $date_added = $prescription[$i]->date_added;
            $type_prescription_id = $prescription[$i]->type_prescription_id;
            $pharmacy_catalog_id_ref = $prescription[$i]->pharmacy_catalog_id_ref;
            $date_0 = $prescription[$i]->date_0;
            $date_1 = $prescription[$i]->checkout_date;
            $how_to_pay = $prescription[$i]->how_to_pay;
            $insurance_name = $prescription[$i]->insurance_name;
            $how_to_pay2 = $prescription[$i]->how_to_pay2;
            $insurance_name2 = $prescription[$i]->insurance_name2;
            $health_facility_name = $prescription[$i]->health_facility_name;
            $norm = $prescription[$i]->norm;
            $patient_name = '-';
            $doctor_name = $prescription[$i]->doctor_name;
            $dpjp_name = $prescription[$i]->dpjp_name;
            $dpjp_name2 = $prescription[$i]->dpjp_name2;
            $health_facility_name = $prescription[$i]->health_facility_name;
            $depo_name = $prescription[$i]->depo_name;
            $catalog_name = $prescription[$i]->catalog_name;
            $package_name = $prescription[$i]->package_name;
            $qty = (float) $prescription[$i]->qty;
            $qty_retur = (float) $prescription[$i]->qty_retur;
            $var_1 = (float) $prescription[$i]->var_1;
            $hna = ($var_1/1.3);
            if ($receipt->pharmacy_catalog_id == $prescription[$i]->pharmacy_catalog_id) {
                $hpp = (float) ($receipt->price_item-(($receipt->price_item*$receipt->discount_item)/100));
                $package_name_receipt = $receipt->package_name_receipt;
            }

            $patient_name = $dt_patient[$norm]['fullname'];
            
            $total = $var_1 * $qty;

            if ($total > 0) {
                if ($hpp > $hna) {
                    $hpp = $hna;
                }
                $total_hpp = $hpp * $qty;
            } else {
                $hpp = 0;
                $total_hpp = 0;
            }

            if ($pharmacy_catalog_id_ref == null) {
                if ($qty_retur != null) {
                    $total = ($qty - $qty_retur) * $var_1;
                    if ($total > 0) {
                        $total_hpp = ($qty - $qty_retur) * $hpp;
                    } else {
                        $hpp = 0;
                        $total_hpp = 0;
                    }
                }
            }

            $coordinate = $i + 2;
            $activeWorksheet->setCellValue('A' . $coordinate, $no_order);
            $activeWorksheet->setCellValue('B' . $coordinate, $date_added);
            $activeWorksheet->setCellValue('C' . $coordinate, $type_prescription_id);
            $activeWorksheet->setCellValue('D' . $coordinate, $date_0);
            $activeWorksheet->setCellValue('E' . $coordinate, $date_1);
            $activeWorksheet->setCellValue('F' . $coordinate, ($how_to_pay != null) ? $how_to_pay : $how_to_pay2);
            $activeWorksheet->setCellValue('G' . $coordinate, $type_prescription_id == 5 ? 'Tunai/Pribadi' : ($insurance_name != null) ? $insurance_name : $insurance_name2);
            $activeWorksheet->setCellValue('H' . $coordinate, $health_facility_name);
            $activeWorksheet->setCellValue('I' . $coordinate, $norm);
            $activeWorksheet->setCellValue('J' . $coordinate, $patient_name);
            $activeWorksheet->setCellValue('K' . $coordinate, ($dpjp_name != null) ? $dpjp_name : $dpjp_name2);
            $activeWorksheet->setCellValue('L' . $coordinate, $doctor_name);
            $activeWorksheet->setCellValue('M' . $coordinate, $health_facility_name);
            $activeWorksheet->setCellValue('N' . $coordinate, $depo_name);
            $activeWorksheet->setCellValue('O' . $coordinate, $catalog_name);
            $activeWorksheet->setCellValue('P' . $coordinate, $qty);
            $activeWorksheet->setCellValue('Q' . $coordinate, $pharmacy_catalog_id_ref == null ? $qty_retur : 0);
            $activeWorksheet->setCellValue('R' . $coordinate, $var_1);
            $activeWorksheet->setCellValue('S' . $coordinate, $package_name);
            $activeWorksheet->setCellValue('T' . $coordinate, $total);
            $activeWorksheet->setCellValue('U' . $coordinate, $hpp);
            $activeWorksheet->setCellValue('V' . $coordinate, $package_name_receipt);
            $activeWorksheet->setCellValue('W' . $coordinate, $total_hpp);

            if ($count % 1000 == 0) {
                Logger::info($this->debug_uniq, 'current processed rows: ' . $count);
            }
        }

        $writer = new Xlsx($spreadsheet);
        
        $filename = $this->name . '_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        Logger::info($this->debug_uniq, "|---- finish export selling prescription ----|");
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $formattedTime = number_format($executionTime, 3, '.', '');
        Logger::info($this->debug_uniq, "execution time: " . $formattedTime . " seconds");

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function get_csv_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
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

        $date_start = $q_strings['date_start'];
        $date_end = $q_strings['date_end'];

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
            'No. Resep' => array(
                'width' => 15
            ),
            'Tanggal Resep' => array(
                'width' => 15
            ),
            'type_prescription_id' => array(
                'width' => 15
            ),
            'date_0' => array(
                'width' => 15
            ),
            'date_1' => array(
                'width' => 15
            ),
            'Cara Bayar' => array(
                'width' => 15
            ),
            'Nama Penjamin' => array(
                'width' => 15
            ),
            'Instalasi' => array(
                'width' => 15
            ),
            'No. RM' => array(
                'width' => 15
            ),
            'Nama Pasien' => array(
                'width' => 15
            ),
            'Nama DPJP' => array(
                'width' => 15
            ),
            'Nama Dokter' => array(
                'width' => 15
            ),
            'Poliklinik' => array(
                'width' => 15
            ),
            'Depo' => array(
                'width' => 15
            ),
            'Catalog' => array(
                'width' => 15
            ),
            'Satuan' => array(
                'width' => 15
            ),
            'Jumlah' => array(
                'width' => 15
            ),
            'Jumlah Retur' => array(
                'width' => 15
            ),
            'Harga' => array(
                'width' => 15
            ),
            'Total Harga' => array(
                'width' => 15
            ),
        );

        $select = "a.no_order, 
        a.date_added, 
        a.type_prescription_id, 
        a.date_0, 
        a.date_1, 
        h.name as how_to_pay, 
        c.name as insurance_name, 
        h2.name as how_to_pay2, 
        c2.name as insurance_name2, 
        hf.name as health_facility_name, 
        a.norm, 
        e.fullname as doctor_name, 
        dpjp.fullname as dpjp_name, 
        dpjp2.fullname as dpjp_name2, 
        hfi.name as health_facility_name, 
        pd.name as depo_name, 
        pc.name as catalog_name, 
        pkg.name as package_name, 
        pi.pharmacy_catalog_id_ref, 
        pi.qty, 
        pi.qty_retur, 
        pi.var_1";
        $where = array(
            'a.is_delete' => 0,
            'pc.category_catalog_id' => '324d6340-db7f-4969-8661-946e5dd159bb',
            'a.flag' => 1,
            'pi.is_delete' => 0,
            'a.date_added >=' => $date_start,
            'a.date_added <=' => $date_end,
        );

        if ($q_strings['health_facility_id'] != null) {
            $where['hf.id'] = $q_strings['health_facility_id'];
        }

        $join = array(
            array('pharmacy_prescription_item pi', 'a.id = pi.pharmacy_prescription_id', 'INNER'),
            array('pharmacy_catalog pc', 'pi.pharmacy_catalog_id = pc.id', 'INNER'),
            array('package pkg', 'pc.package_id = pkg.id', 'INNER'),
            array('pharmacy_depo pd', 'a.pharmacy_depo_id = pd.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'),
            // array('patient ptn', 'a.norm = ptn.norm', 'INNER'),
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
            array('employee dpjp', 'rd.doctor_in_charge_id = dpjp.id', 'LEFT'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary = 1', 'LEFT'),
            array('helper h', 'ri.insurance_id = h.code AND h.variable = "insurance"', 'LEFT'),
            array('company c', 'ri.company_id = c.id', 'LEFT'),
            array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
            array('soap s', 'sp.soap_id = s.id', 'LEFT'),
            array('registration_destination rd2', 's.registration_destination_id = rd2.id', 'LEFT'),
            array('registration_insurance ri2', 'rd2.id = ri2.registration_destination_id AND ri2.is_primary = 1', 'LEFT'),
            array('helper h2', 'ri2.insurance_id = h2.code AND h2.variable = "insurance"', 'LEFT'),
            array('company c2', 'ri2.company_id = c2.id', 'LEFT'),
            array('employee dpjp2', 'rd2.doctor_in_charge_id = dpjp2.id', 'LEFT'),
        );
        $order = array('a.date_added' => 'ASC');
        $this->db->group_by(array("pi.pharmacy_prescription_id", "pi.pharmacy_catalog_id"));

        $startTime = microtime(true);

        Logger::info($this->debug_uniq, "|---- start export selling prescription ----|");

        // $prescription = $this->Prescription->select(0, $select, $where, $join, $order);

        $prescription = $this->db->query("
            SELECT pp.no_order, pp.date_added, pp.type_prescription_id, pp.date_0, pp.date_1,
            h.name as how_to_pay, c.name as insurance_name, h2.name as how_to_pay2, c2.name as insurance_name2,
            hf.name as health_facility_name, pp.norm, e.fullname as doctor_name, dpjp.fullname as dpjp_name, dpjp2.fullname as dpjp_name2,
            hfi.name as health_facility_name, pd.name as depo_name, pc.name as catalog_name, pkg.name as package_name, 
            full_ppi.pharmacy_catalog_id_ref, full_ppi.qty, full_ppi.qty_retur, full_ppi.var_1, ptn.fullname as patient_name
            FROM (
                SELECT ppi.pharmacy_prescription_id, ppi.pharmacy_catalog_id
                FROM pharmacy_prescription_item ppi
                INNER JOIN pharmacy_catalog pc ON ppi.pharmacy_catalog_id = pc.id
                INNER JOIN pharmacy_prescription pp ON ppi.pharmacy_prescription_id = pp.id
                WHERE pp.is_delete = 0
                AND pp.flag = 1
                AND ppi.is_delete = 0
                AND pp.date_added >= '$date_start 00:00:00'
                AND pp.date_added <= '$date_end 23:59:59'
            ) as ppi
            INNER JOIN pharmacy_prescription pp ON ppi.pharmacy_prescription_id = pp.id
            INNER JOIN pharmacy_prescription_item full_ppi ON ppi.pharmacy_prescription_id = full_ppi.pharmacy_prescription_id AND full_ppi.pharmacy_catalog_id = ppi.pharmacy_catalog_id
            INNER JOIN pharmacy_catalog pc ON ppi.pharmacy_catalog_id = pc.id
            INNER JOIN package pkg ON pc.package_id = pkg.id
            INNER JOIN pharmacy_depo pd ON pp.pharmacy_depo_id = pd.id
            INNER JOIN patient ptn ON pp.norm = ptn.norm
            LEFT JOIN health_facility_item hfi ON pp.health_facility_item_id = hfi.id
            LEFT JOIN health_facility hf ON hfi.health_facility_id = hf.id
            LEFT JOIN employee e ON pp.doctor_in_charge_id = e.id
            LEFT JOIN registration_destination rd ON pp.registration_destination_id = rd.id
            LEFT JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
            LEFT JOIN helper h ON ri.insurance_id = h.code AND h.variable = 'insurance'
            LEFT JOIN company c ON ri.company_id = c.id
            LEFT JOIN employee dpjp ON rd.doctor_in_charge_id = dpjp.id
            LEFT JOIN soap_p sp ON pp.soap_p_id = sp.id
            LEFT JOIN soap s ON sp.soap_id = s.id
            LEFT JOIN registration_destination rd2 ON s.registration_destination_id = rd2.id
            LEFT JOIN registration_insurance ri2 ON rd2.id = ri2.registration_destination_id AND ri2.is_primary = 1
            LEFT JOIN helper h2 ON ri2.insurance_id = h2.code AND h2.variable = 'insurance'
            LEFT JOIN company c2 ON ri2.company_id = c2.id
            LEFT JOIN employee dpjp2 ON rd2.doctor_in_charge_id = dpjp2.id
        ")->result();

        // var_dump($this->db->last_query());
        // die();

        // $norms = [];
        // for ($i=0; $i < count($prescription); $i++) { 
        //     $norm = $prescription[$i]->norm;
        //     if ($norm != null) {
        //         $norms[] = $norm;
        //     }
        // }

        // $param = implode(',',$norms);

        // $patient = $this->db->query("
        //     SELECT norm, fullname
        //     FROM patient
        //     WHERE norm in ($param)
        // ")->result();

        // $dt_patient = [];
        // for ($i=0; $i < count($patient); $i++) { 
        //     $dt_patient[$patient[$i]->norm] = array(
        //         'fullname' => $patient[$i]->fullname,
        //     );
        // }
        
        Logger::info($this->debug_uniq, 'total rows: ' . count($prescription));

        // $spreadsheet = new Spreadsheet();
        // $activeWorksheet = $spreadsheet->getActiveSheet();

        $column = 1;
        $row = 1;
        // foreach ($headers as $key => $val) {
        //     foreach ($val as $key_val => $val_val) {
        //         if ($key_val == 'width') {
        //             $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
        //         }
        //     }

        //     // applying style
        //     $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->applyFromArray($style);
        //     // bold text
        //     $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getAlignment()->setHorizontal('center');
        //     // bold text
        //     $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getFont()->setBold(true);
        //     // color cell
        //     $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        //     // set cell value
        //     $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

        //     $column += 1;
        // }

        // Define data to be written (array of arrays)
        $data = [
            ['no_resep','tanggal_resep','type_prescription_id','date_0','date_1','cara_bayar','nama_penjamin','instalasi','norm','nama_pasien','nama_dpjp','nama_dokter','poliklinik','depo','catalog_satuan','jumlah','jumlah_retur','harga','total_harga']
        ];

        $count = 0;
        for ($i=0; $i < count($prescription); $i++) { 
            $count += 1;

            // $activeWorksheet->getstyle('A1')->applyFromArray($style);
            // $activeWorksheet->getstyle('B1')->applyFromArray($style);
            // $activeWorksheet->getstyle('C1')->applyFromArray($style);
            // $activeWorksheet->getstyle('D1')->applyFromArray($style);
            // $activeWorksheet->getstyle('E1')->applyFromArray($style);
            // $activeWorksheet->getstyle('F1')->applyFromArray($style);
            // $activeWorksheet->getstyle('G1')->applyFromArray($style);
            // $activeWorksheet->getstyle('H1')->applyFromArray($style);
            // $activeWorksheet->getstyle('I1')->applyFromArray($style);
            // $activeWorksheet->getstyle('J1')->applyFromArray($style);
            // $activeWorksheet->getstyle('K1')->applyFromArray($style);
            // $activeWorksheet->getstyle('L1')->applyFromArray($style);
            // $activeWorksheet->getstyle('M1')->applyFromArray($style);
            // $activeWorksheet->getstyle('N1')->applyFromArray($style);
            // $activeWorksheet->getstyle('O1')->applyFromArray($style);
            // $activeWorksheet->getstyle('P1')->applyFromArray($style);
            // $activeWorksheet->getstyle('Q1')->applyFromArray($style);
            // $activeWorksheet->getstyle('R1')->applyFromArray($style);
            // $activeWorksheet->getstyle('S1')->applyFromArray($style);
            // $activeWorksheet->getstyle('T1')->applyFromArray($style);

            $no_order = $prescription[$i]->no_order;
            $date_added = $prescription[$i]->date_added;
            $type_prescription_id = $prescription[$i]->type_prescription_id;
            $pharmacy_catalog_id_ref = $prescription[$i]->pharmacy_catalog_id_ref;
            $date_0 = $prescription[$i]->date_0;
            $date_1 = $prescription[$i]->date_1;
            $how_to_pay = $prescription[$i]->how_to_pay;
            $insurance_name = $prescription[$i]->insurance_name;
            $how_to_pay2 = $prescription[$i]->how_to_pay2;
            $insurance_name2 = $prescription[$i]->insurance_name2;
            $health_facility_name = $prescription[$i]->health_facility_name;
            $norm = $prescription[$i]->norm;
            // $patient_name = '-';
            $patient_name = $prescription[$i]->patient_name;
            $doctor_name = $prescription[$i]->doctor_name;
            $dpjp_name = $prescription[$i]->dpjp_name;
            $dpjp_name2 = $prescription[$i]->dpjp_name2;
            $health_facility_name = $prescription[$i]->health_facility_name;
            $depo_name = $prescription[$i]->depo_name;
            $catalog_name = $prescription[$i]->catalog_name;
            $package_name = $prescription[$i]->package_name;
            $qty = (float) $prescription[$i]->qty;
            $qty_retur = (float) $prescription[$i]->qty_retur;
            $var_1 = (float) $prescription[$i]->var_1;

            // $patient_name = $dt_patient[$norm]['fullname'];
            
            $total = $var_1 * $qty;

            if ($pharmacy_catalog_id_ref == null) {
                if ($qty_retur != null) {
                    $total = ($qty - $qty_retur) * $var_1;
                }
            }

            if ($type_prescription_id == 5) {
                $asd = 'Tunai/Pribadi';
            } else {
                if ($insurance_name != null) {
                    $asd = $insurance_name;
                } else {
                    $asd = $insurance_name2;
                }
            }
            
            $dt = [
                $no_order,
                $date_added,
                $type_prescription_id,
                $date_0,
                $date_1,
                ($how_to_pay != null) ? $how_to_pay : $how_to_pay2,
                $asd,
                $health_facility_name,
                $norm,
                $patient_name,
                ($dpjp_name != null) ? $dpjp_name : $dpjp_name2,
                $doctor_name,
                $health_facility_name,
                $depo_name,
                $catalog_name,
                $package_name,
                $qty,
                $pharmacy_catalog_id_ref == null ? $qty_retur : 0,$var_1,$total
            ];

            array_push($data, $dt);

            $coordinate = $i + 2;
            // $activeWorksheet->setCellValue('A' . $coordinate, $no_order);
            // $activeWorksheet->setCellValue('B' . $coordinate, $date_added);
            // $activeWorksheet->setCellValue('C' . $coordinate, $type_prescription_id);
            // $activeWorksheet->setCellValue('D' . $coordinate, $date_0);
            // $activeWorksheet->setCellValue('E' . $coordinate, $date_1);
            // $activeWorksheet->setCellValue('F' . $coordinate, ($how_to_pay != null) ? $how_to_pay : $how_to_pay2);
            // $activeWorksheet->setCellValue('G' . $coordinate, $type_prescription_id == 5 ? 'Tunai/Pribadi' : ($insurance_name != null) ? $insurance_name : $insurance_name2);
            // $activeWorksheet->setCellValue('H' . $coordinate, $health_facility_name);
            // $activeWorksheet->setCellValue('I' . $coordinate, $norm);
            // $activeWorksheet->setCellValue('J' . $coordinate, $patient_name);
            // $activeWorksheet->setCellValue('K' . $coordinate, ($dpjp_name != null) ? $dpjp_name : $dpjp_name2);
            // $activeWorksheet->setCellValue('L' . $coordinate, $doctor_name);
            // $activeWorksheet->setCellValue('M' . $coordinate, $health_facility_name);
            // $activeWorksheet->setCellValue('N' . $coordinate, $depo_name);
            // $activeWorksheet->setCellValue('O' . $coordinate, $catalog_name);
            // $activeWorksheet->setCellValue('P' . $coordinate, $package_name);
            // $activeWorksheet->setCellValue('Q' . $coordinate, $qty);
            // $activeWorksheet->setCellValue('R' . $coordinate, $pharmacy_catalog_id_ref == null ? $qty_retur : 0);
            // $activeWorksheet->setCellValue('S' . $coordinate, $var_1);
            // $activeWorksheet->setCellValue('T' . $coordinate, $total);

            if ($count % 1000 == 0) {
                Logger::info($this->debug_uniq, 'current processed rows: ' . $count);
            }
        }

        // $writer = new Xlsx($spreadsheet);
        
        $filename = $this->name . '_' . date('Ymd_His');
        
        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        // header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        // header('Cache-Control: max-age=0');
        // $writer->save('php://output');

        // Set headers to download the file as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'.csv"');

        // Open the output stream
        $output = fopen('php://output', 'w');

        // Write each row to CSV
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        // Close the output stream
        fclose($output);

        Logger::info($this->debug_uniq, "|---- finish export selling prescription ----|");
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $formattedTime = number_format($executionTime, 3, '.', '');
        Logger::info($this->debug_uniq, "execution time: " . $formattedTime . " seconds");

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}