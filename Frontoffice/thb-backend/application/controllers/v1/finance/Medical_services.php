<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Shared\Date;
class Medical_services extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_doctor_model", "{$this->name}_doctor");
        $this->load->model("v1/tr/Transaction_doctor_model", "Transaction_doctor");
        $this->load->model("v1/hr/Employee_model", "Employee");
        
    }

    public function list_fast_get($p = 0){
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
    
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['doctor_in_charge_name'])) {
            $doctor_in_charge_name = $q_strings['doctor_in_charge_name'];
            $where['a.doctor_in_charge_name'] = $doctor_in_charge_name;
            unset($where['doctor_in_charge_name']);
        }

        if(isset($q_strings['health_facility_id'])) {
            $health_facility_id = $q_strings['health_facility_id'];
            if ($health_facility_id != '40f06534-07ec-4221-ad9d-dea08196079f') {
                $where['a.health_facility_id !='] = '40f06534-07ec-4221-ad9d-dea08196079f';
            } else {
                $where['a.health_facility_id'] = $health_facility_id;
            }
            // $where['a.id !='] = 'd350e55d-6f8a-4c42-b269-cfda717fbf8b';
            // $where['ri.health_facility_item_id !='] = 'd350e55d-6f8a-4c42-b269-cfda717fbf8b';
            $where['a.health_facility_id'] = $health_facility_id;
            unset($where['health_facility_id']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }
        
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'];
            $date_end = $q_strings['date_end'];
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        // $where['a.cost_inacbg >= '] = 2;
        $where['rd.is_cancel'] = 0;
        $where['rd.is_transfer'] = 0;
        $where['rd.register_date >='] = $date_start.' 00:00:00';
        $where['rd.register_date <='] = $date_end.' 23:59:59';
        $this->$model->limit = null;

        $select = 'a.*,e.nickname as dpjp, hfi.health_facility_id, hf.name as health_facility_name, a.doctor_in_charge_name as doctor_in_charge_id, e.fullname as doctor_in_charge_name, bp.name as period_name, rii.service_class_id, rii.service_class_name, rd.registration_id, rd.health_facility_item_id';
        // $select = 'a.*,e.nickname as dpjp, hfi.health_facility_id, hf.name as health_facility_name, a.doctor_in_charge_name as doctor_in_charge_id, e.fullname as doctor_in_charge_name,ri.no_refer,bp.name as period_name, rii.service_class_id, rii.service_class_name, rd.registration_id, rd.health_facility_item_id';
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration_insurance rii', 'rd.id = rii.registration_destination_id AND rii.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            // array('registration r', 'rd.registration_id = r.id', 'INNER'),
            // array('transaction_invoice r', 'rd.registration_id = r.id', 'INNER'),
            array('b_period bp', 'a.period_id = bp.id', 'LEFT'),
            array('employee e', 'a.doctor_in_charge_name = e.id', 'LEFT'),
            // array('refer_internal ri', 'a.registration_destination_id = ri.registration_destination_id', 'LEFT'),
        );
        $order = array('a.sep' => 'ASC');
        if ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
            $this->db->group_by(array("a.sep"));
        } else {
            // $this->db->group_by(array("rd.id"));
        }
        $output = $this->$model->select(0, $select, $where, $join, $order);

        // DOKTER
        $select_d = 'a.*, p.health_facility_item_id, hfi.name as health_facility_item_name, p.name as position_name, p.health_facility_item_id';
        $where_d = array(
            'a.is_delete' => 0,
            'p.is_doctor' => 1,
            'ep.is_intership' => 0,
            'ug.group_id' => 'efb3fdff-fbe3-4954-a391-3729e544b62a'
        );
        $join_d = array(
		    array('employee_position ep', 'ep.employee_id = a.id', 'INNER'),
		    array('user u', 'a.id = u.employee_id', 'INNER'),
		    array('user_group ug', 'u.id = ug.user_id', 'INNER'),
		    array('position p', 'p.id = ep.position_id', 'INNER'),
		    array('health_facility_item hfi', 'hfi.id = p.health_facility_item_id', 'INNER'),
		    array('health_facility hf', 'hf.id = hfi.health_facility_id', 'INNER'),
        );
        $order_d = array('hfi.sort' => 'ASC','a.name' => 'ASC');
        $output_d = $this->Employee->select(0, $select_d, $where_d, $join_d, $order_d);

        for ($i=0; $i < count($output); $i++) {
            $output[$i]->logic = 'Normal';
            if ($output[$i]->no_refer == null) {
                $output[$i]->logic = 'Normal';
            } else if ($output[$i]->no_refer != null) {
                $output[$i]->logic = 'Konsultasi 1 DPJP';
            }

            $implant_data = array();
            $hemodialisa_data = array();

            // Cek Implant
            $implant_data = $this->db->query("
            SELECT `a`.`id`,`pc`.`name` as `pharmacy_catalog_name`,`ppi`.`var_2` as `total_implant`
            FROM `pharmacy_prescription` as `a` 
            INNER JOIN `pharmacy_prescription_item` `ppi` ON `a`.`id` = `ppi`.`pharmacy_prescription_id` 
            INNER JOIN `pharmacy_catalog` `pc` ON `ppi`.`pharmacy_catalog_id` = `pc`.`id` 
            INNER JOIN `transaction_invoice_item` `tii` ON `a`.`id` = `tii`.`transaction_id` 
            INNER JOIN `transaction_invoice` `ti` ON `tii`.`transaction_invoice_id` = `ti`.`id` 
            WHERE `ti`.`registration_id` = '{$output[$i]->registration_id}' AND
            `a`.`is_delete` = 0 AND `ti`.`is_delete` = 0 AND `pc`.`category_catalog_id` = '84eb9c0d-81b1-4d84-9c7d-147d8635bcb9'
            ")->result();

            // cari hd
            if ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                $hemodialisa_data = $this->db->query("
                SELECT `a`.`id`, `a`.`costing_name`, `e`.`fullname` as `specialist_name`, `a`.`var_3`, `a`.`doctor_in_charge_id`, `c`.`classification_one_type_id` , `c`.`classification_three_type_id`
                FROM `transaction_doctor` as `a` 
                INNER JOIN `soap` `s` ON `a`.`soap_id` = `s`.`id` 
                INNER JOIN `registration_destination` `rd` ON `s`.`registration_destination_id` = `rd`.`id` 
                INNER JOIN `employee` `e` ON `a`.`doctor_in_charge_id` = `e`.`id` 
                INNER JOIN `employee_position` `ep` ON `a`.`doctor_in_charge_id` = `ep`.`employee_id` 
                INNER JOIN `position` `p` ON `ep`.`position_id` = `p`.`id` 
                INNER JOIN `costing_services_class` `csc` ON `a`.`costing_id` = `csc`.`id` 
                INNER JOIN `costing` `c` ON `csc`.`costing_id` = `c`.`id` 
                WHERE `rd`.`registration_id` = '{$output[$i]->registration_id}' AND
                `a`.`is_delete` = 0  AND `c`.`classification_one_type_id` = 24
                AND (`c`.`classification_three_type_id` != 63 OR `c`.`classification_three_type_id` IS NULL)
                ")->result();
            }

            // jika ada implant
            if (count($implant_data) > 0) {
                $total_implant = 0;
                for ($im=0; $im < count($implant_data); $im++) { 
                    $total_implant      += $implant_data[$im]->total_implant;
                }
                $output[$i]->total_implant = $total_implant;
                $tarif_ina_tariff       = $output[$i]->cost_inacbg_tariff - $total_implant;
                $tarif_ina              = $tarif_ina_tariff;
            // jika ada hd
            } else if (count($hemodialisa_data) > 0) {
                $total_hemodialisa      = 0;
                $tarif_hd               = 967400;
                $total_hemodialisa      = count($hemodialisa_data);
                $output[$i]->total_hemodialisa = $total_hemodialisa;
                $tarif_ina_tariff       = $output[$i]->cost_inacbg_tariff - ($tarif_hd*$total_hemodialisa);
                $tarif_ina              = $tarif_ina_tariff;
            } else {
                $tarif_ina_tariff       = $output[$i]->cost_inacbg;
                $tarif_ina              = $output[$i]->cost_inacbg;
            }
            
            $tarif_rs                   = $q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f' ? $tarif_ina : $output[$i]->cost_hostpital;
            $tarif_25                   = $q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f' ? $tarif_rs * ($output[$i]->percentage) / 100 : 0;
            $tarif_jm                   = $q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f' ? $tarif_rs - $tarif_25 : 0;
            $basic_percent              = 20;
            if ($q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                $tarif_20               = ($tarif_jm * $basic_percent) / 100;
                $tarif_dokter           = ($tarif_jm * $basic_percent) / 100;
            } else {
                if ($output[$i]->health_facility_item_id == '74087789-8f86-4e45-802b-d3b9710a2a3c') {
                    $basic_percent = 35;
                }
                $tarif_dokter               = $output[$i]->logic == 'Konsultasi 1 DPJP' ? (($tarif_ina * $basic_percent) / 100)/2 : ($tarif_ina * $basic_percent) / 100;
            }

            // Cek nama tindakan
            $tindakan = strtoupper(trim($output[$i]->desc_inacbg ?? ''));

            // cari visit
            $visit_specialist_only = $this->db->query("
            SELECT 
                `a`.`id`, 
                `a`.`costing_name`, 
                `e`.`fullname` AS `specialist_name`, 
                `a`.`var_3`, 
                `a`.`doctor_in_charge_id`, 
                `c`.`classification_one_type_id`, 
                `c`.`classification_three_type_id`
            FROM `transaction_doctor` AS `a`
            INNER JOIN `soap` `s` ON `a`.`soap_id` = `s`.`id`
            INNER JOIN `registration_destination` `rd` ON `s`.`registration_destination_id` = `rd`.`id`
            INNER JOIN `employee` `e` ON `a`.`doctor_in_charge_id` = `e`.`id`
            INNER JOIN `employee_position` `ep` ON `a`.`doctor_in_charge_id` = `ep`.`employee_id`
            INNER JOIN `position` `p` ON `ep`.`position_id` = `p`.`id`
            INNER JOIN `costing_services_class` `csc` ON `a`.`costing_id` = `csc`.`id`
            INNER JOIN `costing` `c` ON `csc`.`costing_id` = `c`.`id`
            WHERE 
                `rd`.`registration_id` = '{$output[$i]->registration_id}' 
                AND `p`.`is_specialist` = 1 
                AND `a`.`is_delete` = 0 
                AND (
                    (`c`.`classification_three_type_id` != 63 
                    AND `c`.`classification_three_type_id` != 62) 
                    OR `c`.`classification_three_type_id` IS NULL
                )
            ")->result();

            // cari visit
            $visit_specialist = $this->db->query("
            SELECT `a`.`id`, `a`.`costing_name`, `e`.`fullname` as `specialist_name`, `a`.`var_3`, `a`.`doctor_in_charge_id`, `c`.`classification_one_type_id` , `c`.`classification_three_type_id`
            FROM `transaction_doctor` as `a` 
            INNER JOIN `soap` `s` ON `a`.`soap_id` = `s`.`id` 
            INNER JOIN `registration_destination` `rd` ON `s`.`registration_destination_id` = `rd`.`id` 
            INNER JOIN `employee` `e` ON `a`.`doctor_in_charge_id` = `e`.`id` 
            INNER JOIN `employee_position` `ep` ON `a`.`doctor_in_charge_id` = `ep`.`employee_id` 
            INNER JOIN `position` `p` ON `ep`.`position_id` = `p`.`id` 
            INNER JOIN `costing_services_class` `csc` ON `a`.`costing_id` = `csc`.`id` 
            INNER JOIN `costing` `c` ON `csc`.`costing_id` = `c`.`id` 
            WHERE `rd`.`registration_id` = '{$output[$i]->registration_id}' AND
            `a`.`is_delete` = 0 
            AND (`c`.`classification_three_type_id` != 63 OR `c`.`classification_three_type_id` IS NULL)
            ")->result();

            // daftar kata kunci yang ingin dicari
            $keywords_specialist = [
                'Visite Dokter Spesialis Ruang Biasa',
                'Visite Dokter Spesialis HCU dan Intensive',
                'Pemeriksaan/Konsultasi Dokter / drg Spesialis IGD By Phone'
            ];

            // $output[$i]->visit_specialist = 0;
            // $output[$i]->visit_specialist_data = array();

            // $output[$i]->visit_specialist_data = array_filter($visit, function($v) use ($keywords_specialist) {
            //     foreach ($keywords_specialist as $word) {
            //         if (isset($v->costing_name) && stripos($v->costing_name, $word) !== false) {
            //             return true;
            //         }
            //     }
            //     return false;
            // });

            $visit_specialist_total             = count($visit_specialist_only);
            $output[$i]->visit_specialist       = $visit_specialist_total;
            $output[$i]->visit_specialist_data  = $visit_specialist_only;
            $output[$i]->implant                = $implant_data;
            $output[$i]->hemodialisa            = $hemodialisa_data;

            // cari inputan OK
            // 1	Orthopaedi dan Traumatologi
            // 10	Anak
            // 11	Mata
            // 2	THT-KL
            // 3	Bedah Plastik
            // 4	Bedah Urologi
            // 47	Cathlab
            // 48	ESWL
            // 5	Bedah Umum
            // 55	Urologi
            // 57	Bedah Mulut
            // 6	Bedah Anak
            // 68	BTKV
            // 69	Spesialis Paru
            // 7	Bedah Mata
            // 70	Bedah Digestif
            // 71	Jantung
            // 8	Obgyn
            // 9	Bedah Saraf
            
            //RANAP
            if ($q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                
                // cari visit spesialis/raber
                $keywords_kode_visit = [
                    'Pemeriksaan/Konsultasi Dokter / drg Sub Spesialis IGD',
                    'Pemeriksaan/Konsultasi Dokter / drg Sub Spesialis IGD By Phone',
                    'Pemeriksaan/Konsultasi Dokter / drg Spesialis IGD By Phone',
                    'Pemeriksaan/Konsultasi Dokter / drg Spesialis Poliklinik',
                    'Pemeriksaan/Konsultasi Dokter / drg Spesialis By Phone',
                    'Pemeriksaan/Konsultasi Dokter / drg Spesialis IGD',
                    'Visite Dokter Spesialis Ruang Biasa',
                    'Visite Dokter Spesialis HCU dan Intensive', 
                    'Visite Dokter Sub Spesialis HCU dan Intensive',
                    'Visite Dokter Sub Spesialis Ruang Biasa'
                ];
                
                $tind_visit = [];

                if (is_array($visit_specialist_only)) {
                    $tind_visit = array_filter($visit_specialist_only, function($v) use ($keywords_kode_visit) {
                        if (!isset($v->costing_name)) return false;
                        return in_array((string)$v->costing_name, $keywords_kode_visit, true);
                    });
                }

                $output[$i]->tind_visit = $tind_visit;

                // cari visit dr umum
                $keywords_kode_visit = [
                    'Visite Dokter Umum Ruang Biasa','Pemeriksaan/Konsultasi Dokter Umum IGD'
                ];
                
                $tind_visit_umum = [];

                if (is_array($visit_specialist)) {
                    $tind_visit_umum = array_filter($visit_specialist, function($v) use ($keywords_kode_visit) {
                        if (!isset($v->costing_name)) return false;
                        return in_array((string)$v->costing_name, $keywords_kode_visit, true);
                    });
                }

                $output[$i]->tind_visit_umum = $tind_visit_umum;

                $keywords_kode_ok = [
                    '1','10','11','2','3','4','47','48','5','55','57','6','68','69','7','70','71','8','9',
                ];
                
                $tind_ok = [];

                if (is_array($visit_specialist)) {
                    $tind_ok = array_filter($visit_specialist, function($v) use ($keywords_kode_ok) {
                        if (!isset($v->classification_one_type_id)) return false;
                        return in_array((string)$v->classification_one_type_id, $keywords_kode_ok, true);
                    });
                }

                $output[$i]->tind_ok = $tind_ok;

                $tind_anes = [];
                $tind_non_anes = [];
                $total_tind_non_anes = 0;

                if ($tind_ok) {

                    $keywords_kode_anestesi = [
                        'Anestesi',
                    ];

                    $tind_anes = [];
    
                    if (is_array($tind_ok)) {
                        $tind_anes = array_filter($tind_ok, function($v) use ($keywords_kode_anestesi) {
                            foreach ($keywords_kode_anestesi as $word) {
                                // pastikan field ada dan cocok (case-insensitive)
                                if (
                                    (isset($v->costing_name) && stripos($v->costing_name, $word) !== false) 
                                ) {
                                    return true;
                                }
                            }
                            return false;
                        });
                    }

                    $output[$i]->tind_anes = $tind_anes;

                    $tind_non_anes = [];
                    $total_tind_non_anes = 1;
    
                    if (is_array($tind_ok)) {
                        $tind_non_anes = array_filter($tind_ok, function($v) use ($keywords_kode_anestesi) {
                            foreach ($keywords_kode_anestesi as $word) {
                                // pastikan field ada dan cocok (case-insensitive)
                                if (
                                    (isset($v->costing_name) && stripos($v->costing_name, $word) === false) 
                                ) {
                                    return true;
                                }
                            }
                            return false;
                        });
                        // pastikan hasilnya masih array dan ada isinya
                        if (!empty($tind_non_anes)) {
                            // Ambil semua ID dokter dari hasil non-anestesi
                            $doctor_ids = array_unique(array_map(function($v) {
                                return $v->doctor_in_charge_id ?? null;
                            }, $tind_non_anes));

                            // Gagalkan jika ditemukan doctor_in_charge_id yang berbeda
                            if (count($doctor_ids) > 1) {
                                // lebih dari 1 dokter → gagalkan
                                $total_tind_non_anes = count($tind_non_anes);
                            }
                        }
                    }

                    $output[$i]->tind_non_anes = $tind_non_anes;
                    $total_tind_non_anes = $total_tind_non_anes;
                }
    
                // Jika tindakan adalah operasi caesar ringan atau sedang, tarif dokter flat Rp 1.000.000
    
                $patient_sc     = false;
                $patient_normal = false;
                $tarif_umum     = 0;
                
                // kondisi px sesar
                if (in_array($tindakan, ['OPERASI PEMBEDAHAN CAESAR (RINGAN)', 'OPERASI PEMBEDAHAN CAESAR (SEDANG)'])) {
                    $patient_sc = true;
                    // sesar kelas 1
                    if ($output[$i]->service_class_id == '8d3b8d25-4606-4db5-9ec1-59ec22514210') {
                        $tarif_dokter   = 1200000;
                        $tarif_anestesi = 480000;
                        $tarif_anak     = 300000;
                        $tarif_umum     = 60000;
                    // sesar kelas 2
                    } else if ($output[$i]->service_class_id == 'f39fbb8f-082e-4c71-8802-92943b6e4683') {
                        $tarif_dokter = 1100000;
                        $tarif_anestesi = 440000;
                        $tarif_anak     = 275000;
                        $tarif_umum     = 60000;
                    // sesar kelas 3
                    } else if ($output[$i]->service_class_id == 'd168732a-fdef-456d-836c-750e003187e0') {
                        $tarif_dokter = 1000000;
                        $tarif_anestesi = 400000;
                        $tarif_anak     = 250000;
                        $tarif_umum     = 60000;
                    }
                // persalinan vaginal
                } else if (in_array($tindakan, ['PERSALINAN VAGINAL (RINGAN)'])) {
                    $patient_sc = true;
                    $tarif_dokter = (($tarif_jm * 18) / 100) * 70 / 100;
                } else {
                    $tarif_dokter = ($tarif_jm * 20) / 100;
                    if (!isset($visit_specialist_total) || $visit_specialist_total <= 0) {
                        $output[$i]->medical_fee = 0;
                    } else {
                        $tarif_dokter = ($tarif_dokter * $visit_specialist_total) / $visit_specialist_total;
                    }
                }
                
                // pembagian per dpjp
                foreach ($output_d as $doctor) {
                    $output[$i]->{$doctor->id} = ''; // default
                    if ($patient_sc) {
                        foreach ($visit_specialist as $visit) {
                            if ($doctor->id === $visit->doctor_in_charge_id) {
                                switch ($doctor->health_facility_item_id) {
                                    case '0a742dd7-3978-4135-b4df-650c66326cf8':
                                        $output[$i]->{$doctor->id} = $tarif_anestesi;
                                        break;
                                    case '00f4dcd5-8577-4e7f-b7b6-58a2a1f767b3':
                                        $output[$i]->{$doctor->id} = $tarif_anak;
                                        break;
                                    case 'cca59ec4-d77a-45fd-b51f-aae069da22ea':
                                        $output[$i]->{$doctor->id} = $tarif_umum;
                                        break;
                                }
                                break; // stop looping once found
                            }
                        }
                    } else {

                        //  Hitung total visit berdasarkan doctor_in_charge_id
                        $visit_count = 0;
                        if (!empty($tind_visit)) {
                            $visit_count = count(array_filter($tind_visit, function($v) use ($doctor) {
                                return isset($v->doctor_in_charge_id) && $v->doctor_in_charge_id === $doctor->id;
                            }));
                        }

                        $check_if_operatie = false;
                        if (count($tind_non_anes) > 0) {
                            foreach ($tind_non_anes as $non_ans) {
                                if (stripos($non_ans->costing_name, 'BTKV') !== false) {
                                    $check_if_operatie = false;
                                } else {
                                    $check_if_operatie = true;
                                }
                            }
                        }

                        if ($visit_count > 0) {
                            // sesar kelas 1
                            if ($output[$i]->service_class_id == '8d3b8d25-4606-4db5-9ec1-59ec22514210') {
                                $tarif_visit     = 40000;
                            // sesar kelas 2
                            } else if ($output[$i]->service_class_id == 'f39fbb8f-082e-4c71-8802-92943b6e4683') {
                                $tarif_visit     = 35000;
                            // sesar kelas 3
                            } else if ($output[$i]->service_class_id == 'd168732a-fdef-456d-836c-750e003187e0') {
                                $tarif_visit     = 30000;
                            }

                            if ($check_if_operatie == true) {
                                $total_visit = $visit_count * $tarif_visit;
                            } else {
                                $total_visit = ($tarif_20 * $visit_count) / count($tind_visit);
                            }

                            // kalau sudah ada nilai sebelumnya (misal tarif umum/anestesi), tambahkan
                            $output[$i]->{$doctor->id} = ($output[$i]->{$doctor->id} ?: 0) + $total_visit;
                        }

                        $output[$i]->check_if_operatie = $check_if_operatie;

                        // $tarif_dokter = (($tarif_jm * 20) / 100)/2;
                        // $output[$i]->{$doctor->id} = $tarif_dokter;
                        
                        //  Hitung total visit umum berdasarkan doctor_in_charge_id
                        $visit_count_umum = 0;
                        if (!empty($tind_visit_umum)) {
                            $visit_count_umum = count(array_filter($tind_visit_umum, function($v) use ($doctor) {
                                return isset($v->doctor_in_charge_id) && $v->doctor_in_charge_id === $doctor->id;
                            }));
                        }

                        if ($visit_count_umum > 0) {
                            // sesar kelas 1
                            if ($output[$i]->service_class_id == '8d3b8d25-4606-4db5-9ec1-59ec22514210') {
                                $tarif_visit_umum     = 40000;
                            // sesar kelas 2
                            } else if ($output[$i]->service_class_id == 'f39fbb8f-082e-4c71-8802-92943b6e4683') {
                                $tarif_visit_umum     = 35000;
                            // sesar kelas 3
                            } else if ($output[$i]->service_class_id == 'd168732a-fdef-456d-836c-750e003187e0') {
                                $tarif_visit_umum     = 30000;
                            }
                            $total_visit_umum = $visit_count_umum * $tarif_visit_umum;

                            // kalau sudah ada nilai sebelumnya (misal tarif umum/anestesi), tambahkan
                            $output[$i]->{$doctor->id} = ($output[$i]->{$doctor->id} ?: 0) + $total_visit_umum;
                        }

                        if ($tind_anes) {
                            foreach ($tind_anes as $ans) {
                                if ($doctor->id === $ans->doctor_in_charge_id) {
                                    switch ($doctor->health_facility_item_id) {
                                        case '0a742dd7-3978-4135-b4df-650c66326cf8':
                                            $output[$i]->{$doctor->id} = ($tarif_jm*7)/100;
                                            break;
                                    }
                                    break; // stop looping once found
                                }
                            }
                        }
                        if ($tind_non_anes) {
                            $tarif_dokter = ($tarif_jm*18)/100;
                            foreach ($tind_non_anes as $non_ans) {
                                if ($doctor->id === $non_ans->doctor_in_charge_id) {
                                    switch ($doctor->health_facility_item_id) {
                                        case '9c89d12e-63c1-4d1e-a082-e37bcadaeec1':
                                            $output[$i]->{$doctor->id} = $tarif_dokter / $total_tind_non_anes;
                                            break;
                                        case '8798e026-a5ad-4b98-8f2b-bfc5521e5144':
                                            $output[$i]->{$doctor->id} = $tarif_dokter / $total_tind_non_anes;
                                            break;
                                        case '820af6c1-0c58-45ae-afb2-990360f9e132':
                                            $output[$i]->{$doctor->id} = $tarif_dokter / $total_tind_non_anes;
                                            break;
                                        case '4adbb716-9cf3-49fa-93b6-ba4bf2f54736':
                                            $output[$i]->{$doctor->id} = $tarif_dokter / $total_tind_non_anes;
                                            break;
                                        case '7759aec0-3f21-42d5-9fad-05d04fb82b24':
                                            $output[$i]->{$doctor->id} = $tarif_dokter / $total_tind_non_anes;
                                            break;
                                        case '74087789-8f86-4e45-802b-d3b9710a2a3c':
                                            $output[$i]->{$doctor->id} = ($output[$i]->cost_inacbg * 35) / 100;
                                            break;
                                        case '73fd97aa-8cec-4058-9bf3-14cdcd9db4c0':
                                            $output[$i]->{$doctor->id} = $tarif_dokter / $total_tind_non_anes;
                                            break;
                                        case '6e90b7af-67ae-4a26-a1e2-44c151719080':
                                            $output[$i]->{$doctor->id} = $tarif_dokter / $total_tind_non_anes;
                                            break;
                                        case '48744dd2-210e-4ee9-bc06-02e333814826':
                                            $output[$i]->{$doctor->id} = $tarif_dokter / $total_tind_non_anes;
                                            break;
                                    }
                                    break; // stop looping once found
                                }
                            }
                            $tarif_dokter = (($tarif_jm*18)/100)/$total_tind_non_anes;
                        }
                    }
                }
            }
            // RAJAL
            if ($q_strings['health_facility_id'] == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') {

                if (in_array($tindakan, ['PROSEDUR ULTRASOUND LAIN-LAIN','PROSEDUR ULTRASOUND GINEKOLOGIK'])) {
                    // $keywords_kode_usg = ['USG'];
    
                    // $output[$i]->tind_usg = [];
    
                    // if (is_array($visit_specialist)) {
                    //     $output[$i]->tind_usg = array_filter($visit_specialist, function($v) use ($keywords_kode_usg) {
                    //         foreach ($keywords_kode_usg as $word) {
                    //             // pastikan field ada dan cocok (case-insensitive)
                    //             if (
                    //                 (isset($v->costing_name) && stripos($v->costing_name, $word) !== false) 
                    //             ) {
                    //                 return true;
                    //             }
                    //         }
                    //         return false;
                    //     });
                    // }
                    // if (!empty($output[$i]->tind_usg)) {
                        if ($output[$i]->health_facility_item_id == '0fd51271-a0e5-4db2-bc6e-12064508e82b') {
                            // Jika ada item USG OBGN, set tarif_dokter = 75000
                            $tarif_dokter = 75000;
                        }
                        if ($output[$i]->health_facility_item_id == '6e90b7af-67ae-4a26-a1e2-44c151719080') {
                            // Jika ada item USG URO, set tarif_dokter = 95000
                            $tarif_dokter = 95000;
                        }
                    // }
                }
            }

            $output[$i]->cost_inacbg_tariff = $tarif_ina_tariff;
            $output[$i]->cost_25            = $tarif_25;
            $output[$i]->cost_20            = $tarif_20;
            $output[$i]->fee_doctor         = $tarif_jm;
            $output[$i]->medical_fee        = $tarif_dokter;

        }
        
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;
        
		$response_data = (object) array(
			'rows' => $output,
			'group' => array(),
			'doctor' => $output_d,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
                
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_fast_resume_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = 'Medical_services_doctor';
    
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // } 
    
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
            unset($where_tr['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
            unset($where_tr['offset']);
        }

        if(isset($q_strings['doctor_in_charge_name'])) {
            $doctor_in_charge_name = $q_strings['doctor_in_charge_name'];
            $where['a.doctor_in_charge_id'] = $doctor_in_charge_name;
            unset($where['doctor_in_charge_name']);
            $where_tr['a.doctor_in_charge_id'] = $doctor_in_charge_name;
            unset($where_tr['doctor_in_charge_name']);
        }

        if(isset($q_strings['health_facility_id'])) {
            $health_facility_id = $q_strings['health_facility_id'];
            $where['hfi.health_facility_id'] = $health_facility_id;
            unset($where['health_facility_id']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }
        
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'];
            $date_end = $q_strings['date_end'];
    
            unset($where['date_start']);
            unset($where['date_end']);
    
            unset($where_tr['date_start']);
            unset($where_tr['date_end']);
        } 

        $where['ms.checkout_date >='] = $date_start;
        $where['ms.checkout_date <='] = $date_end;

        $where_tr['rii.insurance_id'] = 1;
        $where_tr['a.date_added >='] = $date_start;
        $where_tr['a.date_added <='] = $date_end;
        $this->$model->limit = null;

        $select = 'a.*, hfi.health_facility_id, hf.name as health_facility_name, ms.sep, ms.checkin_date, ms.checkin_date,  ms.desc_inacbg, ms.name, e.fullname as doctor_in_charge_name,ri.no_refer,bp.name as period_name, rii.service_class_id, rii.service_class_name, rd.registration_id, rd.health_facility_item_id';
        $join = array(
            array('medical_services ms', 'a.medical_services_id = ms.id', 'INNER'),
            array('registration_destination rd', 'ms.registration_destination_id = rd.id', 'INNER'),
            array('registration_insurance rii', 'rd.id = rii.registration_destination_id AND rii.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            // array('registration r', 'rd.registration_id = r.id', 'INNER'),
            // array('transaction_invoice r', 'rd.registration_id = r.id', 'INNER'),
            array('b_period bp', 'ms.period_id = bp.id', 'LEFT'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
            array('refer_internal ri', 'ms.registration_destination_id = ri.registration_destination_id', 'LEFT'),
        );
        $order = array('ms.checkout_date' => 'ASC');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
        for ($i=0; $i < count($output); $i++) {
        }
        
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;

        // // groupby_doctor

        $select_tr = 'a.id, a.costing_name as desc_inacbg, a.var_3 as cost_doctor, c.id as registration_destination_id, hfi.name as health_facility_name, e.fullname as doctor_in_charge_name,p.norm,p.name,a.date_added as checkin_date';
        $join_tr = array(
            array('soap b', 'a.soap_id = b.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('registration_destination c', 'b.registration_destination_id = c.id', 'INNER'),
            array('registration_insurance rii', 'c.id = rii.registration_destination_id AND rii.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'c.health_facility_item_id = hfi.id', 'INNER'),
            array('registration d', 'c.registration_id = d.id', 'INNER'),
            array('patient p', 'd.norm = p.norm', 'INNER'),
        );
        $order_tr = array('a.date_added' => 'desc');
        $output_tr = $this->Transaction_doctor->select(0, $select_tr, $where_tr, $join_tr, $order_tr);

        for ($x=0; $x < count($output_tr); $x++) { 
            $output_tr[$x]->cost_doctor = ($output_tr[$x]->cost_doctor * 70) / 100;
            $output_tr[$x]->period_name = 'PASIEN UMUM';
            $output_tr[$x]->sep = null;
        }

        $output = array_merge($output,$output_tr);
        
		$response_data = (object) array(
			'rows' => $output,
			'rows_cash' => $output_tr,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
                
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_fast_doctor_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = 'Medical_services_doctor';
    
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // } 
    
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
            unset($where_tr['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
            unset($where_tr['offset']);
        }

        if(isset($q_strings['doctor_in_charge_name'])) {
            $doctor_in_charge_name = $q_strings['doctor_in_charge_name'];
            $where['a.doctor_in_charge_id'] = $doctor_in_charge_name;
            unset($where['doctor_in_charge_name']);
            $where_tr['a.doctor_in_charge_id'] = $doctor_in_charge_name;
            unset($where_tr['doctor_in_charge_name']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['period_id'])) {
            $period_id = $q_strings['period_id'];
            $where['a.period_id'] = $period_id;
            unset($where['period_id']);
            $where_tr['a.period_id'] = $period_id;
            unset($where_tr['period_id']);
        }
        
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        $this->$model->limit = null;

        $select = 'a.*, hfi.health_facility_id, hf.name as health_facility_name, ms.sep, ms.checkin_date, ms.checkin_date,  ms.desc_inacbg, ms.name, e.fullname as doctor_in_charge_name,ri.no_refer,bp.name as period_name, rii.service_class_id, rii.service_class_name, rd.registration_id, rd.health_facility_item_id';
        $join = array(
            array('medical_services ms', 'a.medical_services_id = ms.id', 'INNER'),
            array('registration_destination rd', 'ms.registration_destination_id = rd.id', 'INNER'),
            array('registration_insurance rii', 'rd.id = rii.registration_destination_id AND rii.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            // array('registration r', 'rd.registration_id = r.id', 'INNER'),
            // array('transaction_invoice r', 'rd.registration_id = r.id', 'INNER'),
            array('b_period bp', 'ms.period_id = bp.id', 'LEFT'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
            array('refer_internal ri', 'ms.registration_destination_id = ri.registration_destination_id', 'LEFT'),
        );
        $order = array('ms.checkout_date' => 'ASC');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
        for ($i=0; $i < count($output); $i++) {
        }
        
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;

        // // groupby_doctor

        $select_tr = 'a.id, a.costing_name as desc_inacbg, a.var_3 as cost_doctor, c.id as registration_destination_id, hfi.name as health_facility_name, e.fullname as doctor_in_charge_name,p.norm,p.name,a.date_added as checkin_date';
        $join_tr = array(
            array('soap b', 'a.soap_id = b.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('registration_destination c', 'b.registration_destination_id = c.id', 'INNER'),
            array('registration_insurance rii', 'c.id = rii.registration_destination_id AND rii.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'c.health_facility_item_id = hfi.id', 'INNER'),
            array('registration d', 'c.registration_id = d.id', 'INNER'),
            array('patient p', 'd.norm = p.norm', 'INNER'),
        );
        $order_tr = array('a.date_added' => 'desc');
        $output_tr = $this->Transaction_doctor->select(0, $select_tr, $where_tr, $join_tr, $order_tr);

        for ($x=0; $x < count($output_tr); $x++) { 
            $output_tr[$x]->cost_doctor = ($output_tr[$x]->cost_doctor * 70) / 100;
            $output_tr[$x]->period_name = 'PASIEN UMUM';
            $output_tr[$x]->sep = null;
        }

        $output = array_merge($output,$output_tr);
        
		$response_data = (object) array(
			'rows' => $output,
			'rows_cash' => $output_tr,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
                
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function sendBA_put($p = 0) {
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
                /* Start Transaction */
                if (isset($dt_put['result'])) {

                    $result = $dt_put['result'];
                    $extension = $dt_put['extension'];
                    $name = 'BA';
                    $date_request_raw = time();
                    $unix_time = strtotime($date_request_raw);
                    $date = date('dmY', $unix_time);

                    $name = str_replace(' ', '_', $name);
                    $name = str_replace('/', '', $name);

                    if ($extension == NULL) {
                        $this->REST_Return(400, 'extension field is required');
                        return;
                    }

                    $extension = explode('/', $extension)[1];

                    $bytes = random_bytes(6 / 2);
                    $random_6 = substr(bin2hex($bytes), 0, 6);
                
                    $url = $this->base64ToFile($result, $random_6 . '-' . $name . '-' . $date . '.' . $extension);
                    define('UPLOAD_DIR', 'assets/document/ba/');

                    $spreadsheet = IOFactory::load(UPLOAD_DIR.$url);
                    $sheet = $spreadsheet->getActiveSheet();

                    // Ambil semua data (termasuk header)
                    $data = $sheet->toArray(null, true, true, true);

                    // Hapus file sementara
                    unlink($temp_file);

                    // Ambil baris pertama sebagai header
                    $headerRow = array_shift($data);
                    $headers = [];
                    foreach ($headerRow as $h) {
                        $headers[] = trim((string)$h);
                    }

                    $alias = [
                        'No.SEP' => 'sep',
                        'Tgl. Verifikasi' => 'tgl_verifikasi',
                        'Rill RS' => 'rill_rs',
                        'Diajukan' => 'diajukan',
                        'Disetujui' => 'disetujui'
                    ];

                    // Gabungkan data dengan header + konversi tanggal
                    $result = [];
                    foreach ($data as $row) {
                        $values = array_values($row);
                        $assoc = [];
                    
                        foreach ($headers as $i => $key) {
                            $value = isset($values[$i]) ? $values[$i] : null;
                    
                            // Konversi tanggal Excel
                            if (stripos($key, 'Tgl. Verifikasi') !== false && is_numeric($value)) {
                                $timestamp = Date::excelToTimestamp($value);
                                $value = date('Y-m-d', $timestamp);
                            }
                    
                            // Ganti key pakai alias kalau ada
                            $newKey = isset($alias[$key]) ? $alias[$key] : $key;
                    
                            $assoc[$newKey] = $value;
                        }
                    
                        $result[] = $assoc;
                    }
                    for ($i=0; $i < count($result); $i++) { 
                        $this->db->trans_start();
                            if ($result[$i]['disetujui'] != null || $result[$i]['disetujui'] > 0) {
                                $this->Medical_services->update_v2(
                                    null,
                                    array('period_id'           => $dt_put['period_id']),
                                    array('sep'                 => $result[$i]['sep']),
                                    array('cost_inacbg'         => $result[$i]['disetujui']),
                                    array('cost_inacbg_tariff'  => $result[$i]['disetujui'])
                                );
                            }
                        $this->db->trans_complete();
                        if ($this->db->trans_status() === false) {
                            $response_data = array(
                                'error'     => $this->db->error(),
                                'data'      => 'data',
                                'query'     => $this->db->last_query()
                            );
                            $this->REST_Return(500, 'error while trying to update', $response_data);
                            return;
                        }
                    }
                } 
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $result);
                }
                return;     
            // }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function sendValidation_put($p = 0) {
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
                /* Start Transaction */

                $dataValidation = $dt_put['data'];

                for ($i=0; $i < count($dataValidation); $i++) { 
                    $this->db->trans_start();
                        // if rajal
                        if($dataValidation[$i]['health_facility_id'] == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') {
                            if ($dataValidation[$i]['id']) {
                                $this->Medical_services_doctor->create(
                                    array(
                                        'medical_services_id'    => $dataValidation[$i]['id'],
                                        'doctor_in_charge_id'    => $dataValidation[$i]['doctor_in_charge_id'],
                                        'cost_doctor'            => $dataValidation[$i]['medical_fee']
                                    )
                                );
                            }
                        }
                        // if ranap
                        if($dataValidation[$i]['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                            if (count($dataValidation[$i]['any_dpjp']) > 0) {
                                for ($ia=0; $ia < count($dataValidation[$i]['any_dpjp']); $ia++) { 
                                    $this->Medical_services_doctor->create(
                                        array(
                                            'medical_services_id'    => $dataValidation[$i]['id'],
                                            'doctor_in_charge_id'    => $dataValidation[$i]['any_dpjp'][$ia]['id'],
                                            'cost_doctor'            => $dataValidation[$i]['any_dpjp'][$ia]['value']
                                        )
                                    );
                                }
                            }
                        }

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === false) {
                        $response_data = array(
                            'error'     => $this->db->error(),
                            'data'      => 'data',
                            'query'     => $this->db->last_query()
                        );
                        $this->REST_Return(500, 'error while trying to update', $response_data);
                        return;
                    }
                }
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $result);
                }
                return;     
            // }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function sendValidationPayroll_put($p = 0) {
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
                /* Start Transaction */

                $dataValidation = $dt_put['data'];

                for ($i=0; $i < count($dataValidation); $i++) { 
                    $this->db->trans_start();
                    if ($dataValidation[$i]['period_id'] != null || $dataValidation[$i]['id'] != null) {
                        if ($dataValidation[$i]['sep'] != null || $dataValidation[$i]['sep'] != "") {
                            $this->Medical_services_doctor->replace(
                                array(
                                    'id'                     => $dataValidation[$i]['id'],
                                    'period_id'              => $dataValidation[$i]['period_id'],
                                )
                            );
                        } else {
                            $this->Transaction_doctor->replace(
                                array(
                                    'id'                     => $dataValidation[$i]['id'],
                                    'period_id'              => $dataValidation[$i]['period_id'],
                                )
                            );
                        }
                    }

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === false) {
                        $response_data = array(
                            'error'     => $this->db->error(),
                            'data'      => 'data',
                            'query'     => $this->db->last_query()
                        );
                        $this->REST_Return(500, 'error while trying to update', $response_data);
                        return;
                    }
                }
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $dataValidation);
                }
                return;     
            // }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    private function base64ToFile ($p = 0, $filename = '') {
        define('UPLOAD_DIR', 'assets/document/ba/');
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = UPLOAD_DIR . $file_name;
        $success      = file_put_contents($file, $data);
        $file         = $file_name;
        return $file;
    }

    

    public function margin_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = 'Medical_services_doctor';
    
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // } 
    

        $output = $this->db->query("SELECT
        a.no_order, 
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
        pi.package_name, 
        pi.pharmacy_catalog_id, 
        pi.pharmacy_catalog_id_ref, 
        pi.qty, 
        pi.qty_retur, 
        pi.var_1

        FROM registration_destination rd 

        INNER JOIN registration r ON rd.registration_id = r.id
        INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        INNER JOIN patient p ON r.norm = p.norm
        INNER JOIN helper h ON p.sex = h.code AND h.variable ='sex'
        INNER JOIN soap s ON rd.id = s.registration_destination_id 
        INNER JOIN soap_a sa ON s.id = sa.soap_id 
        INNER JOIN soap_a_action saa ON sa.id = saa.soap_a_id 
        INNER JOIN icd10 i ON saa.icd10_id = i.id 
        INNER JOIN employee e ON rd.doctor_in_charge_id = e.id 
        WHERE rd.checkin_date >= '2024-01-01 00:00:00'
        AND rd.checkin_date <= '2025-10-31 23:59:59'
        AND saa.icd10_id IN ('0256d7e2-1659-4b22-abe7-e913965543a6','514ec2b2-8105-4cbe-ae19-63dc334d55b3','75746717-d920-4148-89c8-ad0f600b8207','87cd59a4-c2c6-4bc9-95d8-15d5b4ebbdbd','ae9abb2d-1edf-4ba3-aabd-559eeae14b0a','cad38aaf-a3a1-409c-9627-900e9818a5b4','de53ebd2-d06f-4d01-88d4-9fd95369cfa1','e766d1e1-ee14-4c97-b0ef-e22b77982a71')
        AND saa.is_delete = 0
        GROUP BY s.id
        ORDER BY rd.register_date DESC
        
        ")->result();

        
        for ($i=0; $i < count($output); $i++) {
            

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