<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
class Control extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library, Bride BPJS
        $this->load->library('Authorization_Token');
        $this->load->library('Vclaim_sep','vclaim_sep');
        // Load Model
        $this->load->model("v1/mr/Insurance_model", "Insurance");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Control_bpjs_model", "Control_bpjs");
        $this->load->model("v1/health_facility/Laboratory_request_model", "Laboratory_request");
        $this->load->model("v1/health_facility/Laboratory_request_item_model", "Laboratory_request_item");
        $this->load->model("v1/health_facility/Radiology_request_model", "Radiology_request");
        $this->load->model("v1/health_facility/Radiology_request_item_model", "Radiology_request_item");
        // Load Helper
        $this->load->helper("excel");
    }

    public function export_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 0); 

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end']) || !isset($q_strings['type'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'];
        $date_end = $q_strings['date_end'];
        $type = $q_strings['type'];

        if ($type == 1){

            $headers = array(
                'phone_number' => array(
                    'width' => 15
                ),
                'full_name' => array(
                    'width' => 15
                ),
                'nama_pasien' => array(
                    'width' => 15
                ),
                'tanggal_control' => array(
                    'width' => 15
                ),
                'poliklinik_tujuan' => array(
                    'width' => 15
                ),
                'nama_dokter_tujuan' => array(
                    'width' => 15
                ),
                'tanggal_kunjungan_terakhir' => array(
                    'width' => 15
                ),
                'kunjungan_terakhir' => array(
                    'width' => 15
                )
            );

        } else if ($type == 2){
            $headers = array(
                'phone_number' => array(
                    'width' => 15
                ),
                'full_name' => array(
                    'width' => 15
                ),
                'nama_pasien' => array(
                    'width' => 15
                ),
                'tanggal_control' => array(
                    'width' => 15
                ),
                'jenis_layanan' => array(
                    'width' => 15
                ),
                'jenis_pemeriksaan' => array(
                    'width' => 15
                )
            );
        }

        if ($type == 3){
            $select = 'a.nama, 
            a.tglRencanaKontrol,
            p.fullname,
            p.phone,
            a.namaDokter,
            hfi.name as health_facility_item_name';
            $where_insu = array(
                'is_post_inpatient`' => '1',
                'tglRencanaKontrol >=' => $date_start,
                'tglRencanaKontrol <=' => $date_end,
            );
            $where_insu['a.noSep'] = null;

            $join_insu = array(
                array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
                array('registration r', 'a.registration_id = r.id', 'INNER'),
                array('patient p', 'r.norm = p.norm', 'INNER'),
                array('health_facility_item hfi', 'a.poliTujuan = hfi.bride_bpjs', 'INNER'),
            );
            $this->db->where('a.noSep IS NULL', null, false);
            $this->db->group_by('a.no_control');
            $output = $this->Control_bpjs->select(0, $select, $where_insu, $join_insu);
        } else {
            $output = $this->vclaim_sep->request("/RencanaKontrol/ListRencanaKontrol/tglAwal/".$date_start."/tglAkhir/".$date_end."/filter/2", "GET");
            $output = json_decode($output);
            $output = $output->response->list;
        }
        // var_dump($output);die();

        $rows = array();

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $column = 1;
        $row = 1;
        foreach ($headers as $key => $val) {
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);
            $column += 1;
        }

        if ($type == 1){
            $count = 0;
            for ($i=0; $i < count($output); $i++) { 
                $count += 1;
                if ($output[$i]->noKartu) {
                    $where['a.number_insurance'] = $output[$i]->noKartu;
                    $select = 'a.norm,p.phone,a.number_insurance,p.fullname';
                    $join = array(
                        array('patient p','a.norm = p.norm','LEFT'),
                    );
                    $output_patient = $this->Insurance->select(0, $select, $where,$join)[0];
                    $flag_check_bpjs_feasible = $this->Registration->check_bpjs_feasible_check_kontrol($output_patient->norm,date('Y-m-d', strtotime($output[$i]->tglRencanaKontrol)));
                    if ($output_patient->phone == '' || $output_patient->phone == null) {
                        $db_sirs = $this->load->database('antrol', true);
                        $db_sirs->select("notelp");
                        $db_sirs->from("bpjs_jkn_antrean");
                        $db_sirs->where("bpjs_jkn_antrean.nomorkartu",$output_patient->number_insurance);
                        $data_f_dba = $db_sirs->get()->result()[0];
                        if ($data_f_dba->notelp) {
                            $output_patient->phone = $data_f_dba->notelp;
                        } else {
                            $output_patient_bpjs = $this->vclaim_sep->request("/Peserta/nokartu/".$output_patient->number_insurance."/tglSEP/".date('Y-m-d'), "GET");
                            $output_patient_bpjs = json_decode($output_patient_bpjs);
                            $output_patient_bpjs = $output->response->peserta;
                            if ($output_patient_bpjs->mr->noTelepon) {
                                $output_patient->phone = $output_patient_bpjs->mr->noTelepon;
                            }
                        }
                    }
                    if ($output_patient->number_insurance == $output[$i]->noKartu) {
                        $phone_number = $output_patient->phone;
                        $full_name = $output[$i]->nama;
                        $nama_pasien = $output_patient->fullname;
                        $tanggal_control = date('d/m/Y', strtotime($output[$i]->tglRencanaKontrol));
                        $poliklinik_tujuan = $output[$i]->namaPoliTujuan;
                        $where_d['a.bride_bpjs'] = $output[$i]->kodeDokter;
                        $select_d = 'a.fullname';
                        $join_d = array();
                        $output_d = $this->Employee->select(0, $select_d, $where_d,$join_d)[0];
                        $nama_dokter_tujuan = $output_d->fullname;
                        $coordinate = $i + 2;
                        if ($flag_check_bpjs_feasible[0]['register_date'] && strtotime($flag_check_bpjs_feasible[0]['register_date']) > strtotime($output[$i]->tglRencanaKontrol.' -7 days')) {
                            $activeWorksheet->getStyle('A'. $coordinate.':H'.$coordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFD966'); // Orange muda
                        } else {
                            $activeWorksheet->getStyle('A'. $coordinate.':H'.$coordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3'); // Hijau muda
                        }
                        $activeWorksheet->setCellValue('A' . $coordinate, $phone_number);
                        $activeWorksheet->setCellValue('B' . $coordinate, $full_name);
                        $activeWorksheet->setCellValue('C' . $coordinate, $nama_pasien);
                        $activeWorksheet->setCellValue('D' . $coordinate, $tanggal_control);
                        $activeWorksheet->setCellValue('E' . $coordinate, $poliklinik_tujuan);
                        $activeWorksheet->setCellValue('F' . $coordinate, $nama_dokter_tujuan);
                        $activeWorksheet->setCellValue('G' . $coordinate, $flag_check_bpjs_feasible[0]['register_date'] ? date('d/m/Y', strtotime($flag_check_bpjs_feasible[0]['register_date'])) : 'Post Inap');
                        $activeWorksheet->setCellValue('H' . $coordinate, $flag_check_bpjs_feasible[0]['health_facility_item_name'] ? $flag_check_bpjs_feasible[0]['health_facility_item_name'] : 'Post Inap');
                    }
                }
            }
        } else if ($type == 1){
            $count = 0;
            for ($i=0; $i < count($output); $i++) { 
                $count += 1;
                if ($output[$i]->noKartu) {
                    $where['a.number_insurance'] = $output[$i]->noKartu;
                    $select = 'p.phone,a.number_insurance,p.fullname';
                    $join = array(
                        array('patient p','a.norm = p.norm','LEFT'),
                    );
                    $output_patient = $this->Insurance->select(0, $select, $where,$join)[0];
                    if ($output_patient->phone == '' || $output_patient->phone == null) {
                        $db_sirs = $this->load->database('antrol', true);
                        $db_sirs->select("notelp");
                        $db_sirs->from("bpjs_jkn_antrean");
                        $db_sirs->where("bpjs_jkn_antrean.nomorkartu",$output_patient->number_insurance);
                        $data_f_dba = $db_sirs->get()->result()[0];
                        if ($data_f_dba->notelp) {
                            $output_patient->phone = $data_f_dba->notelp;
                        } else {
                            $output_patient_bpjs = $this->vclaim_sep->request("/Peserta/nokartu/".$output_patient->number_insurance."/tglSEP/".date('Y-m-d'), "GET");
                            $output_patient_bpjs = json_decode($output_patient_bpjs);
                            $output_patient_bpjs = $output->response->peserta;
                            if ($output_patient_bpjs->mr->noTelepon) {
                                $output_patient->phone = $output_patient_bpjs->mr->noTelepon;
                            }
                        }
                    }
                    if ($output_patient->number_insurance == $output[$i]->noKartu) {
                        $phone_number = $output_patient->phone;
                        $full_name = $output[$i]->nama;
                        $nama_pasien = $output_patient->fullname;
                        $tanggal_control = date('d/m/Y', strtotime($output[$i]->tglRencanaKontrol));
                        $poliklinik_tujuan = $output[$i]->namaPoliTujuan;
                        $where_d['a.bride_bpjs'] = $output[$i]->kodeDokter;
                        $select_d = 'a.fullname';
                        $join_d = array();
                        $output_d = $this->Employee->select(0, $select_d, $where_d,$join_d)[0];
                        $nama_dokter_tujuan = $output_d->fullname;
                        $coordinate = $i + 2;
                        $activeWorksheet->setCellValue('A' . $coordinate, $phone_number);
                        $activeWorksheet->setCellValue('B' . $coordinate, $full_name);
                        $activeWorksheet->setCellValue('C' . $coordinate, $nama_pasien);
                        $activeWorksheet->setCellValue('D' . $coordinate, $tanggal_control);
                        $activeWorksheet->setCellValue('E' . $coordinate, $poliklinik_tujuan);
                        $activeWorksheet->setCellValue('F' . $coordinate, $nama_dokter_tujuan);
                    }
                }
            }
        } else if ($type == 2){
            $count = 0;
            // LAB
            $where_hfl = array(
                'rdi.is_primary' => 1,
                's.is_delete' => 0,
                's.is_booking' => 1,
                'a.date_request >=' => $date_start,
                'a.date_request <=' => $date_end
            );
            $select_hfl = 'a.id, a.date_request,p.fullname,p.phone,rdi.number_insurance';
            $join_hfl = array(
                array('soap s','a.soap_id = s.id','LEFT'),
                array('registration_destination rd','s.registration_destination_id = rd.id','LEFT'),
                array('registration_insurance rdi','s.registration_destination_id = rdi.registration_destination_id','LEFT'),
                array('registration r','rd.registration_id = r.id','LEFT'),
                array('patient p','r.norm = p.norm','LEFT'),
            );
            $output_hfl = $this->Laboratory_request->select(0, $select_hfl, $where_hfl, $join_hfl);
    
            for ($i=0; $i < count($output_hfl); $i++) { 
                $count += 1;
                $where_hfl_item = array(
                    'a.laboratory_request_id' => $output_hfl[$i]->id,
                );
                $select_hfl_item = 'a.id, a.costing_name';
                $join_hfl_item = array();
                $output_hfl_item = $this->Laboratory_request_item->select(0, $select_hfl_item, $where_hfl_item, $join_hfl_item);

                if ($output_hfl[$i]->phone == '' || $output_hfl[$i]->phone == null) {
                    $db_sirs = $this->load->database('antrol', true);
                    $db_sirs->select("notelp");
                    $db_sirs->from("bpjs_jkn_antrean");
                    $db_sirs->where("bpjs_jkn_antrean.nomorkartu",$output_hfl[$i]->number_insurance);
                    $data_f_dba = $db_sirs->get()->result()[0];
                    if ($data_f_dba->notelp) {
                        $output_hfl[$i]->phone = $data_f_dba->notelp;
                    } else {
                        $output_hfl_bpjs = $this->vclaim_sep->request("/Peserta/nokartu/".$output_hfl[$i]->number_insurance."/tglSEP/".date('Y-m-d'), "GET");
                        $output_hfl_bpjs = json_decode($output_hfl_bpjs);
                        $output_hfl_bpjs = $output->response->peserta;
                        if ($output_hfl_bpjs->mr->noTelepon) {
                            $output_hfl[$i]->phone = $output_hfl_bpjs->mr->noTelepon;
                        }
                    }
                }
    
                $phone_number = $output_hfl[$i]->phone;
                $full_name = $output_hfl[$i]->fullname;
                $nama_pasien = $output_hfl[$i]->fullname;
                $tanggal_control = date('d/m/Y', strtotime($output_hfl[$i]->date_request));
                $poliklinik_tujuan = 'Laboratorium';
                $item = '';
                for ($ii=0; $ii < count($output_hfl_item); $ii++) { 
                    $item .= $output_hfl_item[$ii]->costing_name.'-';
                }
                $nama_dokter_tujuan = $item;
    
                $coordinate = $i + 2;
                $activeWorksheet->setCellValue('A' . $coordinate, $phone_number);
                $activeWorksheet->setCellValue('B' . $coordinate, $full_name);
                $activeWorksheet->setCellValue('C' . $coordinate, $nama_pasien);
                $activeWorksheet->setCellValue('D' . $coordinate, $tanggal_control);
                $activeWorksheet->setCellValue('E' . $coordinate, $poliklinik_tujuan);
                $activeWorksheet->setCellValue('F' . $coordinate, $nama_dokter_tujuan);
            }
    
            // RAD
            $where_hfr = array(
                'rdi.is_primary' => 1,
                's.is_delete' => 0,
                's.is_booking' => 1,
                'a.date_request >=' => $date_start,
                'a.date_request <=' => $date_end
            );
            $select_hfr = 'a.id, a.date_request,p.fullname,p.phone,rdi.number_insurance';
            $join_hfr = array(
                array('soap s','a.soap_id = s.id','LEFT'),
                array('registration_destination rd','s.registration_destination_id = rd.id','LEFT'),
                array('registration_insurance rdi','s.registration_destination_id = rdi.registration_destination_id','LEFT'),
                array('registration r','rd.registration_id = r.id','LEFT'),
                array('patient p','r.norm = p.norm','LEFT'),
            );
            $output_hfr = $this->Radiology_request->select(0, $select_hfr, $where_hfr, $join_hfr);
    
            for ($i=0; $i < count($output_hfr); $i++) { 
                $count += 1;
                $where_hfr_item = array(
                    'a.radiology_request_id' => $output_hfr[$i]->id,
                );
                $select_hfr_item = 'a.id, a.costing_name';
                $join_hfr_item = array();
                $output_hfr_item = $this->Radiology_request_item->select(0, $select_hfr_item, $where_hfr_item, $join_hfr_item);

                if ($output_hfr[$i]->phone == '' || $output_hfr[$i]->phone == null) {
                    $db_sirs = $this->load->database('antrol', true);
                    $db_sirs->select("notelp");
                    $db_sirs->from("bpjs_jkn_antrean");
                    $db_sirs->where("bpjs_jkn_antrean.nomorkartu",$output_hfr[$i]->number_insurance);
                    $data_f_dba = $db_sirs->get()->result()[0];
                    if ($data_f_dba->notelp) {
                        $output_hfr[$i]->phone = $data_f_dba->notelp;
                    } else {
                        $output_hfr_bpjs = $this->vclaim_sep->request("/Peserta/nokartu/".$output_hfr[$i]->number_insurance."/tglSEP/".date('Y-m-d'), "GET");
                        $output_hfr_bpjs = json_decode($output_hfr_bpjs);
                        $output_hfr_bpjs = $output->response->peserta;
                        if ($output_hfr_bpjs->mr->noTelepon) {
                            $output_hfr[$i]->phone = $output_hfr_bpjs->mr->noTelepon;
                        }
                    }
                }
    
                $phone_number = $output_hfr[$i]->phone;
                $full_name = $output_hfr[$i]->fullname;
                $nama_pasien = $output_hfr[$i]->fullname;
                $tanggal_control = date('d/m/Y', strtotime($output_hfr[$i]->date_request));
                $poliklinik_tujuan = 'Radiologi';
                $item = '';
                for ($ii=0; $ii < count($output_hfr_item); $ii++) { 
                    $item .= $output_hfr_item[$ii]->costing_name.'-';
                }
                $nama_dokter_tujuan = $item;
    
                $coordinate = $i + 2;

                $activeWorksheet->setCellValue('A' . $coordinate, $phone_number);
                $activeWorksheet->setCellValue('B' . $coordinate, $full_name);
                $activeWorksheet->setCellValue('C' . $coordinate, $nama_pasien);
                $activeWorksheet->setCellValue('D' . $coordinate, $tanggal_control);
                $activeWorksheet->setCellValue('E' . $coordinate, $poliklinik_tujuan);
                $activeWorksheet->setCellValue('F' . $coordinate, $nama_dokter_tujuan);
            }
        } else if ($type == 3){
            for ($i=0; $i < count($output); $i++) { 
                $phone_number = $output[$i]->phone;
                $full_name = $output[$i]->fullname;
                $nama_pasien = $output[$i]->fullname;
                $tanggal_control = date('d/m/Y', strtotime($output[$i]->tglRencanaKontrol));
                $poliklinik_tujuan = $output[$i]->health_facility_item_name;
                $nama_dokter_tujuan = $output[$i]->namaDokter;

                $coordinate = $i + 2;
                $activeWorksheet->setCellValue('A' . $coordinate, $phone_number);
                $activeWorksheet->setCellValue('B' . $coordinate, $full_name);
                $activeWorksheet->setCellValue('C' . $coordinate, $nama_pasien);
                $activeWorksheet->setCellValue('D' . $coordinate, $tanggal_control);
                $activeWorksheet->setCellValue('E' . $coordinate, $poliklinik_tujuan);
                $activeWorksheet->setCellValue('F' . $coordinate, $nama_dokter_tujuan);
            }
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'remainder_template_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}