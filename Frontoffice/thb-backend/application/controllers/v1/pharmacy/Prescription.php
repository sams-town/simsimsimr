<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Prescription extends My_Controller {

    /** 4C-05: cache no_rawat per prescription_id untuk audit (1 query per request). */
    protected $_no_rawat_cache = array();

    /* ==========================================================================
     * 4C-05 — AUTHORIZATION SCOPE FARMASI (branch server-side, fail-closed)
     * ==========================================================================
     * Semua endpoint farmasi mengambil branch HANYA dari token. Branch kosong
     * atau objek milik branch lain => ditolak sebelum data dikirim, dan setiap
     * penolakan diaudit (DENIED_*) dengan user, branch, aksi, dan no_rawat.
     */

    /** Branch dari token; kosong / token tak valid => tolak (respons terkirim). */
    protected function _ph_branch($operation) {
        $t = $this->authorization_token->validateToken();
        if (empty($t) OR $t['status'] !== TRUE) {
            $this->REST_Return(401, isset($t['message']) ? $t['message'] : 'Invalid Token.', 'Invalid Token.');
            return null;
        }
        $branch = trim((string) $this->security->xss_clean($t['data']->institute_branch_id));
        // Tahap 1: mode single cabang => branch kosong memakai default (config/branch_default.php).
        // TODO: expand branch filter saat multi-cabang
        $branch = $this->resolve_branch_id($branch);
        if ($branch === '') {
            $this->_c2_audit('DENIED_BRANCH_SCOPE_UNDEFINED', null,
                $this->security->xss_clean($t['data']->id),
                array('reason' => 'branch_empty', 'operation' => $operation), 409);
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return null;
        }
        return $branch;
    }

    /** no_rawat (nomor kunjungan) untuk audit; null bila resep bebas / tanpa registrasi. */
    protected function _ph_no_rawat($prescription_id) {
        if ($prescription_id === null || $prescription_id === '' || $prescription_id === 0) {
            return null;
        }
        $key = (string) $prescription_id;
        if (array_key_exists($key, $this->_no_rawat_cache)) {
            return $this->_no_rawat_cache[$key];
        }
        $q = $this->db->query(
            "SELECT r.no_rawat
               FROM pharmacy_prescription p
               LEFT JOIN registration_destination rd ON rd.id = p.registration_destination_id
               LEFT JOIN registration r ON r.id = rd.registration_id
              WHERE p.id = ?",
            array($key));
        $row = ($q === FALSE) ? null : $q->row();
        return $this->_no_rawat_cache[$key] = ($row === null ? null : $row->no_rawat);
    }

    /** Audit penolakan scope (tanpa rollback: dipakai endpoint baca). */
    protected function _ph_deny($code, $prescription_id, $user_id, $operation, $detail, $http, $message) {
        $this->_c2_audit('DENIED_' . $code, $prescription_id, $user_id,
            array_merge(array('operation'    => $operation,
                              'applied_by'   => $user_id,
                              'applied_from_branch' => isset($this->s1_actor['branch']) ? $this->s1_actor['branch'] : null), (array) $detail), $http);
        $this->REST_Return($http, $message, (array) $detail, $code);
    }

    /** Objek tunggal: resep harus ada DAN milik branch token. */
    protected function _ph_object_scope($prescription_id, $branch, $operation) {
        $q = $this->db->query(
            "SELECT id, no_order, institute_branch_id FROM pharmacy_prescription WHERE id = ?",
            array($prescription_id));
        $row = ($q === FALSE) ? null : $q->row();
        $uid = isset($this->s1_actor['user_id']) ? $this->s1_actor['user_id'] : null;
        if ($row === null) {
            $this->_ph_deny('PHARMACY_RX_NOT_FOUND', $prescription_id, $uid, $operation,
                array('reason' => 'not_found'), 404,
                'Prescription with id ' . $prescription_id . ' does not exist!');
            return false;
        }
        if ((string) $row->institute_branch_id !== (string) $branch) {
            $this->_ph_deny('PHARMACY_BRANCH_SCOPE_MISMATCH', $prescription_id, $uid, $operation,
                array('reason' => 'branch_mismatch', 'object_branch' => $row->institute_branch_id,
                      'token_branch' => $branch), 403,
                'Data berada di luar branch scope.');
            return false;
        }
        return true;
    }

    /** Depo harus milik branch token (endpoint katalog/stok). */
    protected function _ph_depo_scope($depo_id, $branch, $operation) {
        $q = $this->db->query(
            "SELECT id, institute_branch_id FROM pharmacy_depo WHERE id = ? AND is_delete = 0",
            array($depo_id));
        $row = ($q === FALSE) ? null : $q->row();
        $uid = isset($this->s1_actor['user_id']) ? $this->s1_actor['user_id'] : null;
        if ($row === null) {
            $this->_ph_deny('PHARMACY_DEPO_NOT_FOUND', null, $uid, $operation,
                array('reason' => 'depo_not_found', 'pharmacy_depo_id' => $depo_id), 404,
                'Depo tidak ditemukan.');
            return false;
        }
        if (trim((string) $row->institute_branch_id) !== (string) $branch) {
            $this->_ph_deny('PHARMACY_DEPO_SCOPE_MISMATCH', null, $uid, $operation,
                array('reason' => 'depo_branch_mismatch', 'pharmacy_depo_id' => $depo_id,
                      'depo_branch' => $row->institute_branch_id, 'token_branch' => $branch), 403,
                'Depo berada di luar branch scope.');
            return false;
        }
        return true;
    }

    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Catalog_model", "Catalog");
        $this->load->model("$this->directory/Prescription_short_model", "Prescription_short");
        $this->load->model("$this->directory/Depo_model", "Depo");
        $this->load->model("$this->directory/Pharmacy_assets_model", "Assets");
        $this->load->model("$this->directory/Pharmacy_assets_card_model", "Assets_card");
        $this->load->model("$this->directory/Pharmacy_assets_price_model", "Assets_price");
        $this->load->model("$this->directory/Kardex_model", "Kardex");
        $this->load->model("$this->directory/Kardex_item_model", "Kardex_item");
        $this->load->model("$this->directory/Kardex_stock_model", "Kardex_stock");
        $this->load->model("$this->directory/Kardex_stock_concoction_model", "Kardex_stock_concoction");
        $this->load->model("$this->directory/Signatura_times_model", "Signatura_times");
        $this->load->model("$this->directory/Catalog_instruction_model", "Administer");
        $this->load->model("v1/mr/Registration_val_model", "Registration_val");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Soap_p_model", "Soap_p");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/mr/Sbar_model", "Sbar");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/Package_model", "Package");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/Unit_model", "Unit");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        $this->load->model("v1/Display_queue_model", "Queue_display");
        $this->load->model("v1/setting/Queue_status_model", "Queue_status");
        // Load Helper
        $this->load->helper('queue');
        $this->load->helper('utils');

        $this->create_rules = array(
            array('field' => 'form_prescription_parent[]', 'label' => 'form_prescription_parent', 'rules' => 'trim|required'),
            array('field' => 'form_prescription[]', 'label' => 'form_prescription', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'form_prescription_parent[]', 'label' => 'form_prescription_parent', 'rules' => 'trim|required'),
            array('field' => 'form_prescription[]', 'label' => 'form_prescription', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->queue_next_rules = array(
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim|required'),
        );

        $this->change_catalog_rules = array(
            array('field' => 'pharmacy_catalog_id', 'label' => 'pharmacy_catalog_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'pharmacy_catalog_id_ref', 'label' => 'pharmacy_catalog_id_ref', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'user', 'label' => 'user', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'used', 'label' => 'used', 'rules' => 'trim'),
            array('field' => 'notes', 'label' => 'notes', 'rules' => 'trim'),
            array('field' => 'administer_id', 'label' => 'administer_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'dose', 'label' => 'dose', 'rules' => 'trim'),
            array('field' => 'dose_unit_id', 'label' => 'dose_unit_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'signatura_times_id', 'label' => 'signatura_times_id', 'rules' => 'trim|max_length[36]'),
        );
    }

    /**
     * Prescription API
     * --------------------
     * @param: pharmacy_depo_id
     * @param: soap_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: date_added
     * --------------------
     * @method : POST
     * @link: version/mr/prescription
     **/

     public function create_old_post() {
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
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('soap_p_id', 'soap_p_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_in_charge_id', 'doctor_in_charge_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
            $this->form_validation->set_rules('date_added', 'date_added', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);

                if(count($dt_post['form_prescription']) > 0) {
                    $registration_destination_id = $dt_post['registration_destination_id'];
                    $type_prescription_id = $dt_post['type_prescription_id'];

                    $select = 'a.id, a.health_facility_item_id';
                    $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

                    if (count($registration_destination) == 0) {
                        $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
                        return;
                    }

                    $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

                    $select = 'a.id, a.health_facility_id';
                    $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);

                    $health_facility_id = $health_facility_item[0]->health_facility_id;
                    
                    // count no_order & queue
                    // if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
                    //     $prefix = 'PRJ';
                    // } else {
                    //     $prefix = 'PRS';
                    // }

                    if ($type_prescription_id == 1) {
                        $prefix = 'PRJ';
                    } else {
                        $prefix = 'PRS';
                    }

                    $epoch = time();
                    $dt_queue = array(
                        'var_2' => date('Y', $epoch),
                        'var_3' => date('m', $epoch),
                        'var_4' => date('d', $epoch),
                        'var_5' => date('Y-m-d', $epoch),
                    );

                    $no_order = counter_queue($prefix, $dt_queue, 4);
                    $queue = round((int)substr($no_order, -4));

                    $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];

                    $employee = $this->Employee->select_id($doctor_in_charge_id);

                    if ($employee == '') {
                        $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
                        return;
                    }

                    $select = 'a.id, c.norm';
                    $where = array('b.id' => $registration_destination_id);
                    $registration = $this->Registration->select(0, $select, $where);

                    $norm = $registration[0]->norm;

                    $soap_p_id = $dt_post['soap_p_id'];

                    $soap_p = $this->Soap_p->select_id($soap_p_id);

                    if ($dt_post['type_prescription_id'] == 3) {
                        $soap_p = '';
                    } else {
                        if ($soap_p == '') {
                            $this->REST_Return(404, 'there is no soap_p with id ' . $soap_p_id);
                            return;
                        }
                    }

                    $dt_prescription = array(
                        'norm'                          => $norm,
                        'institute_branch_id'           => $institute_branch_id,
                        'pharmacy_depo_id'              => $dt_post['pharmacy_depo_id'],
                        'type_prescription_id'          => $dt_post['type_prescription_id'] ? $dt_post['type_prescription_id'] : 1,
                        'registration_destination_id'   => $dt_post['registration_destination_id'],
                        'soap_p_id'                     => $soap_p_id,
                        'health_facility_item_id'       => $health_facility_item_id,
                        'doctor_in_charge_id'           => $dt_post['doctor_in_charge_id'],
                        'no_order'                      => $no_order,
                        // 'var_1'                     => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
                        // 'var_2'                     => isset($dt_post['var_2']) ? $dt_post['var_2'] : '',
                        // 'var_3'                     => isset($dt_post['var_3']) ? $dt_post['var_3'] : '',
                        'date_added'                => date("Y-m-d H:i:s",time()),
                        'date_0'                    => date("Y-m-d H:i:s",time()),
                        'flag'                      => 0,
                        'queue'                     => $queue,
                    );
                    $id_prescription = $this->Prescription->create($dt_prescription);

                    // INIT_PRESCRIPTION_ITEM
                    for ($i=0; $i < count($dt_post['form_prescription']); $i++) { 
                        for ($ip=0; $ip < count($dt_post['form_prescription_parent']); $ip++) { 
                            if ($dt_post['form_prescription_parent'][$ip]['id'] == $dt_post['form_prescription'][$i]['prescription_type_id']) {
                                $dt_prescription_item = array(
                                    'pharmacy_prescription_id'      => $id_prescription,
                                    'pharmacy_catalog_id'           => $dt_post['form_prescription'][$i]['pharmacy_catalog_id'],
                                    'prescription_item_type_id'     => $dt_post['form_prescription'][$i]['prescription_type_id'],
                                    'prescription_item_type_used'   => $dt_post['form_prescription_parent'][$ip]['used'],
                                    'prescription_item_type_value'  => $dt_post['form_prescription_parent'][$ip]['value'],
                                    'prescription_item_type_package'  => $dt_post['form_prescription_parent'][$ip]['package'],
                                    'package_id'                    => $dt_post['form_prescription'][$i]['package_id'],
                                    'package_name'                  => $dt_post['form_prescription'][$i]['package_name'],
                                    'dose'                          => isset($dt_post['form_prescription'][$i]['dose']) ? $dt_post['form_prescription'][$i]['dose'] : '',
                                    'dose_doctor'                   => isset($dt_post['form_prescription'][$i]['sum_dose']) ? intval($dt_post['form_prescription'][$i]['sum_dose']) : '',
                                    'qty_doctor'                    => isset($dt_post['form_prescription'][$i]['value_package']) ? $dt_post['form_prescription'][$i]['value_package'] : '',
                                    'qty'                           => isset($dt_post['form_prescription'][$i]['value_package']) ? $dt_post['form_prescription'][$i]['value_package'] : '',
                                    'qty_copy'                      => isset($dt_post['form_prescription'][$i]['qty_copy']) ? $dt_post['form_prescription'][$i]['qty_copy'] : '',
                                    // 'var_1'                         => isset($dt_post['form_prescription'][$i]['var_1']) ? $dt_post['form_prescription'][$i]['var_1'] : '',
                                    // 'var_2'                         => isset($dt_post['form_prescription'][$i]['var_2']) ? $dt_post['form_prescription'][$i]['var_2'] : '',
                                    // 'var_3'                         => isset($dt_post['form_prescription'][$i]['var_3']) ? $dt_post['form_prescription'][$i]['var_3'] : '',
                                    'sort'                          => isset($dt_post['form_prescription'][$i]['sort']) ? $dt_post['form_prescription'][$i]['sort'] : '',
                                    'used'                          => isset($dt_post['form_prescription'][$i]['used']) ? $dt_post['form_prescription'][$i]['used'] : '',
                                    'desc'                          => isset($dt_post['form_prescription'][$i]['desc']) ? $dt_post['form_prescription'][$i]['desc'] : '',
                                );
                            }
                        }
                        $this->Prescription_item->replace($dt_prescription_item);
                    }
                }

                $response_data = array(
                    'id'     => $id_prescription
                );
                $this->REST_Return(201, 'SUCCESS', $response_data);
                return;
                // // Check pharmacy_depo_id & soap_id
                // if($this->Depo->read($dt_post['pharmacy_depo_id'], false) != [] && $this->Soap->read($dt_post['soap_id'], false) != []) {
                //     $model = $this->name;
                //     /* Start Transaction */
                //     $this->db->trans_start();
                //     // Count no_request
                //     $this->$model->whereFields['a.date_added >='] = date('Y-m-d').' 00:00:00';
                //     $this->$model->whereFields['a.date_added <='] = date('Y-m-d').' 23:59:59';
                //     $this->$model->whereFields['pharmacy_depo_id'] = $dt_post['pharmacy_depo_id'];
                //     $this->$model->whereFields['a.is_delete'] = 0;
                //     $this->$model->orderFields = array('a.no_order' => 'DESC');
                //     $this->$model->limit = 1;
                //     $tmp = $this->$model->read(0,false);
                //     $waktu = date("Ymd",time());
                //     if($tmp != []) {
                //         $no_order = 'PRS' . $waktu . str_pad(intval(substr($tmp[0]->no_order,11)) + 1, 4, '0', STR_PAD_LEFT);
                //         // Check if reset is needed
                //         if(substr($tmp[0]->no_order,3,8) != date('Ymd',time())) {
                //             $no_order = 'PRS' . $waktu . '0001';
                //         }
                //     } else {
                //         $no_order = 'PRS' . $waktu . '0001';
                //     }

                //     // Count queue
                //     $this->$model->whereFields['pharmacy_depo_id'] = $dt_post['pharmacy_depo_id'];
                //     $this->$model->whereFields['a.is_delete'] = 0;
                //     $this->$model->orderFields = array('a.queue' => 'DESC');
                //     $this->$model->limit = 1;
                //     $tmp = $this->$model->read(0,false);
                //     if($tmp != []) {
                //         $queue = $tmp[0]->queue + 1;
                //         // Check if reset is needed
                //         if(substr($tmp[0]->no_order,3,8) != date('Ymd',time())) {
                //             $queue = 1;
                //         }
                //     } else {
                //         $queue = 1;
                //     }
                    
                //     $dt_soap = $this->Soap->read($dt_post['soap_id'], false)[0];

                //     $soap_p_id = $this->Soap_p->create(array('soap_id'=> $dt_post['soap_id']));
                //     // $soap_p_id = 1;
                //     $dt_prescription = array(
                //         'norm'                      => $dt_post['norm'],
                //         'institute_branch_id'       => $institute_branch_id,
                //         'pharmacy_depo_id'          => $dt_post['pharmacy_depo_id'],
                //         'soap_p_id'                 => $soap_p_id,
                //         'health_facility_item_id'   => $dt_post['health_facility_item_id'],
                //         'doctor_in_charge_id'       => $dt_soap->doctor_in_charge_id,
                //         'no_order'                  => $no_order,
                //         'var_1'                     => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
                //         'var_2'                     => isset($dt_post['var_2']) ? $dt_post['var_2'] : '',
                //         'var_3'                     => isset($dt_post['var_3']) ? $dt_post['var_3'] : '',
                //         'date_added'                => $dt_post['date_added'].' 00:00:00',
                //         // 'date_added'                => date("Y-m-d H:i:s",time()),
                //         'date_0'                    => date("Y-m-d H:i:s",time()),
                //         'flag'                      => 0,
                //         'queue'                     => $queue,
                //     );
                //     // var_dump($dt_prescription);
                //     $id = $this->$model->create($dt_prescription);
                //     // $id = 1;

                //     // INIT_PRESCRIPTION_ITEM
                //     for ($i=0; $i < count($dt_post['form_prescription']); $i++) { 
                //         // Check pharmacy_catalog_id
                //         if($this->Catalog->read($dt_post['form_prescription'][$i]['pharmacy_catalog_id'], false) != []) {
                //             // "code": 17,
                //             // "name": "Sangobion Tab",
                //             // "pharmacy_catalog_id": "ea467fbe-21a0-11ef-89a9-0242ac140006",
                //             // "package_id": "3e9850aa-3505-4e47-bf44-3ee2c04b81fa",
                //             // "dose": 1,
                //             // "sum_dose": "1 Pcs",
                //             // "package_name": "Tablet",
                //             // "prescription_type_id": 1,
                //             // "prescription_type_name": "OBAT SATUAN",
                //             // "value_doctor": 0,
                //             // "value_package": "2",
                //             // "used": "",
                //             // "desc": "",
                //             // "sort": 0
                //             $dt_prescription_item = array(
                //                 'pharmacy_prescription_id'      => $id,
                //                 'pharmacy_catalog_id'           => $dt_post['form_prescription'][$i]['pharmacy_catalog_id'],
                //                 'prescription_item_type_id'     => $dt_post['form_prescription'][$i]['prescription_type_id'],
                //                 'package_id'                    => $dt_post['form_prescription'][$i]['package_id'],
                //                 'package_name'                  => $dt_post['form_prescription'][$i]['package_name'],
                //                 'dose'                          => isset($dt_post['form_prescription'][$i]['dose']) ? $dt_post['form_prescription'][$i]['dose'] : '',
                //                 'dose_doctor'                   => isset($dt_post['form_prescription'][$i]['sum_dose']) ? intval($dt_post['form_prescription'][$i]['sum_dose']) : '',
                //                 'qty_doctor'                    => isset($dt_post['form_prescription'][$i]['value_package']) ? $dt_post['form_prescription'][$i]['value_package'] : '',
                //                 'qty'                           => isset($dt_post['form_prescription'][$i]['value_package']) ? $dt_post['form_prescription'][$i]['value_package'] : '',
                //                 'qty_copy'                      => isset($dt_post['form_prescription'][$i]['qty_copy']) ? $dt_post['form_prescription'][$i]['qty_copy'] : '',
                //                 'var_1'                         => isset($dt_post['form_prescription'][$i]['var_1']) ? $dt_post['form_prescription'][$i]['var_1'] : '',
                //                 'var_2'                         => isset($dt_post['form_prescription'][$i]['var_2']) ? $dt_post['form_prescription'][$i]['var_2'] : '',
                //                 'var_3'                         => isset($dt_post['form_prescription'][$i]['var_3']) ? $dt_post['form_prescription'][$i]['var_3'] : '',
                //                 'sort'                          => isset($dt_post['form_prescription'][$i]['sort']) ? $dt_post['form_prescription'][$i]['sort'] : '',
                //                 'used'                          => isset($dt_post['form_prescription'][$i]['used']) ? $dt_post['form_prescription'][$i]['used'] : '',
                //                 'desc'                          => isset($dt_post['form_prescription'][$i]['desc']) ? $dt_post['form_prescription'][$i]['desc'] : '',
                //             );

                //             $this->Prescription_item->replace($dt_prescription_item);
                //         } else {
                //             $this->REST_Return(404, 'There is no pharmacy_catalog_id with id ' . $dt_post['form_prescription'][$i]['pharmacy_catalog_id']);
                //             return;
                //         }
                //         // var_dump($dt_prescription_item);
                //     }
                //     $this->db->trans_complete();
                //     /* End Transaction */
                //     if ($this->db->trans_status() === false) {
                //         $response_data = $this->db->trans_status();
                //         $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                //     } else {
                //         $response_data = array(
                //             'id'     => $id
                //         );
                //         $this->REST_Return(201, 'SUCCESS', $response_data);
                //     }
                // } else {
                //     $this->REST_Return(404, 'Wrong pharmacy_depo_id or soap_id!');
                // }
                // return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);

        // 4C-03C: branch hanya dari token. Kosong => fail-closed sebelum transaksi.
        $token_branch   = trim((string) $institute_branch_id);
        $token_username = isset($is_valid_token['data']->username) ? $is_valid_token['data']->username : null;
        if ($token_branch === '') {
            $this->_c30_audit('DENIED_BRANCH_SCOPE_UNDEFINED', null, $user_id, $token_username,
                array('reason' => 'branch_empty'), 409, 'create');
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return;
        }

        $registration_destination_id = $dt_post['registration_destination_id'];
        $type_prescription_id = $dt_post['type_prescription_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
        $soap_p_id = $dt_post['soap_p_id'];
        $pharmacy_depo_id = $dt_post['pharmacy_depo_id'];
        
        if ($type_prescription_id === '' || $type_prescription_id === null) {
            $type_prescription_id = 1;
        }

        $select = 'a.id, a.health_facility_item_id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id, c.norm';
        $where = array('b.id' => $registration_destination_id);
        $registration = $this->Registration->select(0, $select, $where);

        $norm = $registration[0]->norm;
        
        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $select = 'a.id, a.health_facility_id';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);

        $health_facility_id = $health_facility_item[0]->health_facility_id;
        
        // count no_order & queue
        // if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
        //     $prefix = 'PRJ';
        // } else {
        //     $prefix = 'PRS';
        // }

        if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
            $pharmacy_depo_id = '972dcaba-cea0-4515-9756-75ea675c6322';
        }

        // 4C-03C: depo & identitas penjamin dibaca dari DB, bukan dipercaya dari payload.
        $depo_q   = $this->db->query("SELECT id, institute_branch_id, is_delete FROM pharmacy_depo WHERE id = ?", array($pharmacy_depo_id));
        $depo_row = ($depo_q === FALSE) ? null : $depo_q->row();
        if ($depo_row === null || (int) $depo_row->is_delete === 1) {
            $this->_c30_audit('DENIED_DEPO_NOT_FOUND', null, $user_id, $token_username,
                array('reason' => 'depo_not_found', 'pharmacy_depo_id' => $pharmacy_depo_id), 409, 'create');
            $this->REST_Return(409, 'Depo farmasi tidak ditemukan.', array(
                'pharmacy_depo_id' => $pharmacy_depo_id,
            ), 'DEPO_NOT_FOUND');
            return;
        }
        $depo_branch = trim((string) $depo_row->institute_branch_id);
        if ($depo_branch !== '' && $depo_branch !== $token_branch) {
            $this->_c30_audit('DENIED_DEPO_SCOPE_MISMATCH', null, $user_id, $token_username,
                array('reason' => 'depo_branch_mismatch', 'depo_branch' => $depo_branch, 'token_branch' => $token_branch), 403, 'create');
            $this->REST_Return(403, 'Depo berada di luar branch scope.', array(), 'DEPO_SCOPE_MISMATCH');
            return;
        }

        $c3c_identity  = $this->_c3c_identity($registration_destination_id);
        $c3c_date      = date('Y-m-d');
        $c3c_snapshots = array();

        // ALKES GUARD: alkes (category "Alat Kesehatan") bukan obat dan tidak boleh
        // masuk resep. Fail-closed sebelum transaksi supaya tidak ada baris tertulis.
        $alkes_rows = $this->_alkes_in_prescription($dt_post);
        if (!empty($alkes_rows)) {
            $this->_c30_audit('DENIED_ALKES_IN_PRESCRIPTION', null, $user_id, $token_username,
                array('items' => $alkes_rows), 422, 'create');
            $this->REST_Return(422, 'Item alat kesehatan tidak bisa diresepkan.', array(
                'items' => $alkes_rows,
            ), 'ALKES_NOT_PRESCRIBABLE');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
            $prefix = 'PRJ';
        } else {
            $prefix = 'PRS';
        }

        $epoch = time();
        $dt_queue = array(
            'var_2' => date('Y', $epoch),
            'var_3' => date('m', $epoch),
            'var_4' => date('d', $epoch),
            'var_5' => date('Y-m-d', $epoch),
        );

        $no_order = counter_queue($prefix, $dt_queue, 4);
        $queue = round((int)substr($no_order, -4));

        if ($type_prescription_id != 3) { // IF NOT BHP
            if ($soap_p_id === '' || $soap_p_id === null) {
                $this->REST_Return(400, 'soap_p_id is required');
                return;
            }

            $select = 'a.id';
            $soap_p = $this->Soap_p->select($soap_p_id, $select);
            if (count($soap_p) == 0) {
                $this->REST_Return(404, 'there is no soap_p with id ' . $soap_p_id);
                return;
            }
        }

        $form_prescription = $dt_post['form_prescription'];
        $form_prescription_parent = $dt_post['form_prescription_parent'];

        $dt_prescription = array(
            'norm'                          => $norm,
            'institute_branch_id'           => $institute_branch_id,
            'pharmacy_depo_id'              => $pharmacy_depo_id,
            'type_prescription_id'          => $type_prescription_id,
            'registration_destination_id'   => $registration_destination_id,
            'soap_p_id'                     => $soap_p_id,
            'health_facility_item_id'       => $health_facility_item_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'no_order'                      => $no_order,
            // 'var_1'                     => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
            // 'var_2'                     => isset($dt_post['var_2']) ? $dt_post['var_2'] : '',
            // 'var_3'                     => isset($dt_post['var_3']) ? $dt_post['var_3'] : '',
            'date_added'                    => date("Y-m-d H:i:s",time()),
            'date_0'                        => date("Y-m-d H:i:s",time()),
            'flag'                          => 0,
            'queue'                         => $queue,
        );
        $id_prescription = $this->Prescription->create($dt_prescription);

        for ($i=0; $i < count($form_prescription); $i++) { 
            $prescription_type_id = $form_prescription[$i]['prescription_type_id'];

            $signatura_times_id = $form_prescription[$i]['signatura_times_id'] ?? null;
            $administer_id = $form_prescription[$i]['administer_id'] ?? null;
            $dose_unit_id = $form_prescription[$i]['dose_unit_id'] ?? null;
            $desc = clean_postput($form_prescription[$i]['desc'] ?? null);
            $status_kardex = clean_postput($form_prescription[$i]['status_kardex'] ?? null);

            if ($signatura_times_id != null) {
                $select = 'a.id';
                $signatura_times = $this->Signatura_times->select($signatura_times_id, $select);
                if (count($signatura_times) == 0) {
                    $this->REST_Return(404, 'signatura times with id ' . $signatura_times_id . ' does not exist');
                    return;
                }
            }

            if ($administer_id != null) {
                $select = 'a.id';
                $administer = $this->Administer->select($administer_id, $select);
                if (count($administer) == 0) {
                    $this->REST_Return(404, 'administer with id ' . $administer_id . ' does not exist');
                    return;
                }
            }

            if ($dose_unit_id != null) {
                $select = 'a.id';
                $dose_unit = $this->Unit->select($dose_unit_id, $select);
                if (count($dose_unit) == 0) {
                    $this->REST_Return(404, 'unit with id ' . $dose_unit_id . ' does not exist');
                    return;
                }
            }

            for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                $form_prescription_parent_id = $form_prescription_parent[$ip]['id'];
                $form_prescription_parent_signatura_times_id = $form_prescription_parent[$ip]['signatura_times_id'];
                $form_prescription_parent_administer_id = $form_prescription_parent[$ip]['administer_id'];
                $form_prescription_parent_desc = clean_postput($form_prescription_parent[$ip]['desc'] ?? null);
                $form_prescription_parent_status_kardex = clean_postput($form_prescription_parent[$ip]['status_kardex'] ?? null);

                if ($form_prescription_parent_id == $prescription_type_id) {
                    if ($form_prescription_parent_signatura_times_id != null) {
                        $select = 'a.id';
                        $signatura_times = $this->Signatura_times->select($form_prescription_parent_signatura_times_id, $select);
                        if (count($signatura_times) == 0) {
                            $this->REST_Return(404, 'signatura times with id ' . $form_prescription_parent_signatura_times_id . ' does not exist');
                            return;
                        }
                        $signatura_times_id = $form_prescription_parent_signatura_times_id;
                    }

                    if ($form_prescription_parent_administer_id != null) {
                        $select = 'a.id';
                        $administer = $this->Administer->select($form_prescription_parent_administer_id, $select);
                        if (count($administer) == 0) {
                            $this->REST_Return(404, 'administer with id ' . $form_prescription_parent_administer_id . ' does not exist');
                            return;
                        }
                        $administer_id = $form_prescription_parent_administer_id;
                    }

                    if ($form_prescription_parent_desc != null) {
                        $desc = $form_prescription_parent_desc;
                    }

                    if ($form_prescription_parent_status_kardex != null) {
                        $status_kardex = $form_prescription_parent_status_kardex;
                    }

                    $dt_prescription_item = array(
                        'pharmacy_prescription_id'      => $id_prescription,
                        'pharmacy_catalog_id'           => $form_prescription[$i]['pharmacy_catalog_id'],
                        'prescription_item_type_id'     => $form_prescription[$i]['prescription_type_id'],
                        'prescription_item_type_used'   => $form_prescription_parent[$ip]['used'],
                        'prescription_item_type_value'  => $form_prescription_parent[$ip]['value'],
                        'prescription_item_type_package'=> $form_prescription_parent[$ip]['package'],
                        'package_id'                    => $form_prescription[$i]['package_id'],
                        'package_name'                  => $form_prescription[$i]['package_name'],
                        'signatura_times_id'            => $signatura_times_id,
                        'administer_id'                 => $administer_id,
                        'dose'                          => isset($form_prescription[$i]['dose']) ? $form_prescription[$i]['dose'] : '',
                        'dose_unit_id'                  => $dose_unit_id,
                        'dose_doctor'                   => isset($form_prescription[$i]['sum_dose']) ? intval($form_prescription[$i]['sum_dose']) : '',
                        'qty_doctor'                    => isset($form_prescription[$i]['value_package']) ? $form_prescription[$i]['value_package'] : '',
                        'qty'                           => isset($form_prescription[$i]['value_package']) ? $form_prescription[$i]['value_package'] : '',
                        'qty_copy'                      => isset($form_prescription[$i]['qty_copy']) ? $form_prescription[$i]['qty_copy'] : '',
                        // 'var_1'                         => isset($form_prescription[$i]['var_1']) ? $form_prescription[$i]['var_1'] : '',
                        // 'var_2'                         => isset($form_prescription[$i]['var_2']) ? $form_prescription[$i]['var_2'] : '',
                        // 'var_3'                         => isset($form_prescription[$i]['var_3']) ? $form_prescription[$i]['var_3'] : '',
                        'sort'                          => isset($form_prescription[$i]['sort']) ? $form_prescription[$i]['sort'] : '',
                        'used'                          => isset($form_prescription[$i]['used']) ? $form_prescription[$i]['used'] : '',
                        'desc'                          => $desc,
                        'status_kardex'                 => $status_kardex,
                    );
                }
            }

            // 4C-03C: harga canonical dari server. Harga/var_1 kiriman client tidak pernah dibaca.
            $c3c = $this->_c3c_resolve_price($form_prescription[$i]['pharmacy_catalog_id'], $token_branch,
                $pharmacy_depo_id, $c3c_identity['service_class_id'], $c3c_identity['insurance_id'], $c3c_date);
            if ($c3c['ok'] !== true) {
                // Satu item gagal => seluruh create di-rollback, tidak ada snapshot parsial.
                $this->db->trans_rollback();
                $this->_c30_audit('DENIED_' . $c3c['code'], $id_prescription, $user_id, $token_username, array(
                    'reason'       => $c3c['code'],
                    'catalog_id'   => $form_prescription[$i]['pharmacy_catalog_id'],
                    'depo_id'      => $pharmacy_depo_id,
                    'branch_id'    => $token_branch,
                    'class_id'     => $c3c_identity['service_class_id'],
                    'insurance_id' => $c3c_identity['insurance_id'],
                    'price_date'   => $c3c_date,
                ), 409, 'create');
                $this->REST_Return(409, ($c3c['code'] === 'PRICE_AMBIGUOUS'
                        ? 'Harga katalog ambigu; tidak ada satu harga canonical.'
                        : 'Harga katalog tidak ditemukan untuk dimensi resep ini.'),
                    array('reason' => $c3c['code'], 'pharmacy_catalog_id' => $form_prescription[$i]['pharmacy_catalog_id']),
                    $c3c['code']);
                return;
            }
            $dt_prescription_item = array_merge($dt_prescription_item, $this->_c3c_snapshot($c3c['row']));
            $c3c_snapshots[] = array(
                'pharmacy_catalog_id' => $form_prescription[$i]['pharmacy_catalog_id'],
                'price_id'            => $c3c['row']->id,
                'price_sale'          => $c3c['row']->price_sale,
            );

            $this->Prescription_item->replace($dt_prescription_item);
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_prescription,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create prescription', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id_prescription
        );

        // 4C-03C: audit harga server yang dipakai (bukan harga client).
        $this->_c30_audit('PRICE_SNAPSHOT_OK', $id_prescription, $user_id, $token_username, array(
            'branch_id'    => $token_branch,
            'depo_id'      => $pharmacy_depo_id,
            'class_id'     => $c3c_identity['service_class_id'],
            'insurance_id' => $c3c_identity['insurance_id'],
            'items'        => $c3c_snapshots,
        ), 200, 'create');

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        // 4C-05: scope branch fail-closed (objek harus milik branch token).
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        if (!$this->_ph_object_scope($id, $ph_branch, 'read')) { return; }
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        if (!$id){
            // daftar field yang dianggap penting (wajib ada salah satunya)
            $requiredFields = [
                'registration_id',
                'registration_destination_id',
                'soap_id',
                'norm',
                'doctor_in_charge_id',
            ];
    
            // cek apakah ada salah satu field yang tidak kosong
            $hasRequired = false;
            foreach ($requiredFields as $field) {
                if (!empty($q_strings[$field])) {
                    $hasRequired = true;
                    break;
                // } else {
                //     if ($id != null || $id != "" ){
                //         $hasRequired = true;
                //         break;
                //     }
                }
            }
    
            
            if (!$hasRequired) {
                $this->REST_Return(400, 'Minimal salah satu parameter wajib diisi.');
                return;
            }
        }

        $pharmacy_prescription_id = $id;

        $where = $q_strings;

        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {;
            $this->$model->limit = $q_strings['limit']; 
            $this->$model->offset = $q_strings['offset']; 
            unset($where['limit']);
            unset($where['offset']);
        }

        if(isset($q_strings['registration_destination_id'])){
            $where['a.registration_destination_id'] = $q_strings['registration_destination_id'];
            unset($where['registration_destination_id']);
        }

        if(isset($q_strings['norm'])){
            $where['a.norm'] = $q_strings['norm'];
            unset($where['norm']);
        }

        if(isset($q_strings['doctor_in_charge_id'])) {
            $where['a.doctor_in_charge_id'] = $q_strings['doctor_in_charge_id'];
            unset($where['doctor_in_charge_id']);
        }

        if(isset($q_strings['soap_id'])) {
            $where['sp.soap_id'] = $q_strings['soap_id'];
            unset($where['soap_id']);
        }
        
        $select = 'a.*,
		d.name as depo_name,
		p.name as patient_name, p.birth_date as patient_birth_date, p.birth_place as patient_birth_place, p.birth_place as patient_birth_place, p.sex, p.phone, p.address,
		hfi.name as health_facility_item_name,
		coalesce(a.registration_destination_id, s.registration_destination_id) as registration_destination_id, sp.soap_id,
		hri.name as insurance_name, c.name as company_name, cc.name as company_name_cob, ri.number_insurance, ri.sep,
		e.fullname as doctor_in_charge_name,
		rd.queue as queue_poli,
		rd.code_queue as code_queue_poli,
		rd.checkout_date,
		dpjp.fullname as doctor_in_charge_first_name,
		hf.name as status,
		hf2.name as flag_prescription';
        $where['a.is_delete'] = 0;
        $join = array(
            array('pharmacy_depo d', 'a.pharmacy_depo_id = d.id', 'LEFT'),
            array('patient p', 'a.norm = p.norm', 'LEFT'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
            array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
            array('soap s', 'sp.soap_id = s.id', 'LEFT'),
            array('registration_insurance ri', 'coalesce(a.registration_destination_id, s.registration_destination_id) = ri.registration_destination_id AND ri.is_primary ="1"', 'LEFT'),
            array('registration_destination rd', 'coalesce(a.registration_destination_id, s.registration_destination_id) = rd.id', 'LEFT'),
            array('company c', 'ri.company_id = c.id', 'LEFT'),
            array('company cc', 'ri.company_id_cob = cc.id', 'LEFT'),
            array('helper hri', 'ri.insurance_id = hri.code AND hri.variable="insurance"', 'LEFT'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
            array('employee dpjp', 's.doctor_in_charge_id = dpjp.id', 'LEFT'),
            array('helper hf', 'a.flag = hf.code AND hf.variable = "type_prescription_flag"'),
            array('helper hf2', 'a.type_prescription_id = hf2.code AND hf2.variable = "type_prescription"')
        );
        $order = array(
            'a.date_added' => 'DESC'
        );
        if ($pharmacy_prescription_id === 0 || $pharmacy_prescription_id === "0") {
            if(isset($q_strings['registration_id'])){
                $registration_id = $q_strings['registration_id'];
                $output = $this->db->query("
                    -- Case 1: a.registration_destination_id is NOT NULL
                    SELECT a.*,
                    d.name as depo_name,
                    p.name as patient_name, p.birth_date as patient_birth_date, p.birth_place as patient_birth_place, p.birth_place as patient_birth_place, p.sex, p.phone, p.address,
                    hfi.name as health_facility_item_name,
                    a.registration_destination_id, sp.soap_id,
                    hri.name as insurance_name, c.name as company_name, cc.name as company_name_cob, ri.number_insurance, ri.sep,
                    e.fullname as doctor_in_charge_name,
                    rd.queue as queue_poli,
                    rd.code_queue as code_queue_poli,
                    dpjp.fullname as doctor_in_charge_first_name,
                    hf.name as status,
                    hf2.name as flag_prescription
                    FROM pharmacy_prescription AS a
                    LEFT JOIN pharmacy_depo d ON a.pharmacy_depo_id = d.id
                    LEFT JOIN patient p ON a.norm = p.norm
                    LEFT JOIN health_facility_item hfi ON a.health_facility_item_id = hfi.id
                    LEFT JOIN soap_p sp ON a.soap_p_id = sp.id
                    LEFT JOIN soap s ON sp.soap_id = s.id
                    JOIN registration_destination rd ON a.registration_destination_id = rd.id
                    JOIN registration_insurance ri ON a.registration_destination_id = ri.registration_destination_id AND ri.is_primary = '1'
                    LEFT JOIN company c ON ri.company_id = c.id
                    LEFT JOIN company cc ON ri.company_id_cob = cc.id
                    LEFT JOIN helper hri ON ri.insurance_id = hri.code AND hri.variable='insurance'
                    LEFT JOIN employee e ON a.doctor_in_charge_id = e.id
                    LEFT JOIN employee dpjp ON s.doctor_in_charge_id = dpjp.id
                    JOIN helper hf ON a.flag = hf.code AND hf.variable = 'type_prescription_flag'
                    JOIN helper hf2 ON a.type_prescription_id = hf2.code AND hf2.variable = 'type_prescription'
                    WHERE rd.registration_id = '$registration_id'
                    AND a.is_delete = 0

                    UNION ALL

                    -- Case 2: a.registration_destination_id IS NULL, use soap.registration_destination_id
                    SELECT a.*,
                    d.name as depo_name,
                    p.name as patient_name, p.birth_date as patient_birth_date, p.birth_place as patient_birth_place, p.birth_place as patient_birth_place, p.sex, p.phone, p.address,
                    hfi.name as health_facility_item_name,
                    s.registration_destination_id, sp.soap_id,
                    hri.name as insurance_name, c.name as company_name, cc.name as company_name_cob, ri.number_insurance, ri.sep,
                    e.fullname as doctor_in_charge_name,
                    rd.queue as queue_poli,
                    rd.code_queue as code_queue_poli,
                    dpjp.fullname as doctor_in_charge_first_name,
                    hf.name as status,
                    hf2.name as flag_prescription
                    FROM pharmacy_prescription AS a
                    LEFT JOIN pharmacy_depo d ON a.pharmacy_depo_id = d.id
                    LEFT JOIN patient p ON a.norm = p.norm
                    LEFT JOIN health_facility_item hfi ON a.health_facility_item_id = hfi.id
                    LEFT JOIN soap_p sp ON a.soap_p_id = sp.id
                    LEFT JOIN soap s ON sp.soap_id = s.id
                    JOIN registration_destination rd ON s.registration_destination_id = rd.id
                    JOIN registration_insurance ri ON s.registration_destination_id = ri.registration_destination_id AND ri.is_primary = '1'
                    LEFT JOIN company c ON ri.company_id = c.id
                    LEFT JOIN company cc ON ri.company_id_cob = cc.id
                    LEFT JOIN helper hri ON ri.insurance_id = hri.code AND hri.variable='insurance'
                    LEFT JOIN employee e ON a.doctor_in_charge_id = e.id
                    LEFT JOIN employee dpjp ON s.doctor_in_charge_id = dpjp.id
                    JOIN helper hf ON a.flag = hf.code AND hf.variable = 'type_prescription_flag'
                    JOIN helper hf2 ON a.type_prescription_id = hf2.code AND hf2.variable = 'type_prescription'
                    WHERE (a.registration_destination_id IS NULL OR a.registration_destination_id = '') 
                    AND rd.registration_id = '$registration_id'
                    AND a.is_delete = 0

                    ORDER BY date_added DESC
                ")->result();
            } else {
                $output = $this->$model->select(0, $select, $where, $join, $order);
            }
        } else {
            $output = $this->$model->select($pharmacy_prescription_id, $select, $where, $join);
        }

        for ($i=0; $i < count($output); $i++) { 
            $this->Prescription_item->whereFields['a.is_delete'] = 0;
            $form_prescription = $this->Prescription_item->read($output[$i]->id);
            if ($form_prescription != []) {
                $form_prescription_parent = array();

                for ($j=0; $j < count($form_prescription); $j++) { 
                    $pharmacy_prescription_id = $form_prescription[$j]->pharmacy_prescription_id;
                    $pharmacy_catalog_id = $form_prescription[$j]->pharmacy_catalog_id;
                    $package_id = $form_prescription[$j]->package_id;

                    $kardex_status = null;
                    $kardex_is_change = null;
                    $kardex_dose_unit_id = null;
                    $kardex_dose_unit_name = null;
                    $kardex_signatura_times_id = null;
                    $kardex_signatura_times_name = null;
                    $kardex_administer_id = null;
                    $kardex_administer_name = null;

                    $select = 'a.id, ki.dose_unit_id, u.name as dose_unit_name, ki.signatura_times_id, st.name as signatura_times_name, ki.administer_id, pci.name as administer_name, ki.status, ki.is_change';
                    $where = array(
                        'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
                        'ki.pharmacy_catalog_id' => $pharmacy_catalog_id,
                    );
                    $join = array(
                        array('kardex_item ki', 'a.id = ki.kardex_id', 'INNER'),
                        array('unit u', 'ki.dose_unit_id = u.id', 'INNER'),
                        array('signatura_times st', 'ki.signatura_times_id = st.id', 'INNER'),
                        array('pharmacy_catalog_instruction pci', 'ki.administer_id = pci.id', 'INNER'),
                    );
                    $kardex = $this->Kardex->select(0, $select, $where, $join);
                    if (count($kardex) > 0) {
                        $kardex_status = $kardex[0]->status;
                        $kardex_is_change = $kardex[0]->is_change;
                        $kardex_dose_unit_id = $kardex[0]->dose_unit_id;
                        $kardex_dose_unit_name = $kardex[0]->dose_unit_name;
                        $kardex_signatura_times_id = $kardex[0]->signatura_times_id;
                        $kardex_signatura_times_name = $kardex[0]->signatura_times_name;
                        $kardex_administer_id = $kardex[0]->administer_id;
                        $kardex_administer_name = $kardex[0]->administer_name;
                    }

                    $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                    $output[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $catalog->name;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $catalog->code;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_dose'] = $catalog->dose;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_dose_unit_id'] = $catalog->dose_unit_id;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_dose_unit_name'] = $catalog->dose_unit_name;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_packaging_value'] = $catalog->packaging_value;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_packaging_unit_id'] = $catalog->packaging_unit_id;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_packaging_unit_name'] = $catalog->packaging_unit_name;
                    $output[$i]->form_prescription[$j]['is_chronic'] = $catalog->is_chronic;
                    $output[$i]->form_prescription[$j]['restriction'] = $catalog->restriction;
                    $output[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                    $output[$i]->form_prescription[$j]['prescription_item_type_package'] = $form_prescription[$j]->prescription_item_type_package;
                    $output[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
                    $output[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
                    $output[$i]->form_prescription[$j]['signatura_times_id'] = $form_prescription[$j]->signatura_times_id;
                    $output[$i]->form_prescription[$j]['signatura_times_name'] = $form_prescription[$j]->signatura_times_name;
                    $output[$i]->form_prescription[$j]['administer_id'] = $form_prescription[$j]->administer_id;
                    $output[$i]->form_prescription[$j]['administer_name'] = $form_prescription[$j]->administer_name;
                    $output[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
                    $output[$i]->form_prescription[$j]['dose_unit_id'] = $form_prescription[$j]->dose_unit_id;
                    $output[$i]->form_prescription[$j]['dose_unit_name'] = $form_prescription[$j]->dose_unit_name;
                    $output[$i]->form_prescription[$j]['dose_name'] = $catalog->dose_name;
                    $output[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                    $output[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
                    $output[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
                    $output[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                    $output[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
                    $output[$i]->form_prescription[$j]['qty_retur'] = $form_prescription[$j]->qty_retur;
                    $output[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                    $output[$i]->form_prescription[$j]['is_retur'] = $form_prescription[$j]->is_retur;
                    $output[$i]->form_prescription[$j]['is_retur_full'] = $form_prescription[$j]->is_retur_full;
                    $output[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
                    $output[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
                    $output[$i]->form_prescription[$j]['var_1'] = $form_prescription[$j]->var_1;
                    $output[$i]->form_prescription[$j]['var_2'] = $form_prescription[$j]->var_2;
                    $output[$i]->form_prescription[$j]['var_3'] = $form_prescription[$j]->var_3;
                    $output[$i]->form_prescription[$j]['var_4'] = $form_prescription[$j]->var_4;
                    $output[$i]->form_prescription[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_id_retur'] = $form_prescription[$j]->pharmacy_catalog_id_retur;
                    $output[$i]->form_prescription[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
                    $output[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
                    $output[$i]->form_prescription[$j]['status_kardex'] = $form_prescription[$j]->status_kardex;
                    $output[$i]->form_prescription[$j]['kardex_status'] = $kardex_status;
                    $output[$i]->form_prescription[$j]['kardex_is_change'] = $kardex_is_change;
                    $output[$i]->form_prescription[$j]['kardex_dose_unit_id'] = $kardex_dose_unit_id;
                    $output[$i]->form_prescription[$j]['kardex_dose_unit_name'] = $kardex_dose_unit_name;
                    $output[$i]->form_prescription[$j]['kardex_signatura_times_id'] = $kardex_signatura_times_id;
                    $output[$i]->form_prescription[$j]['kardex_signatura_times_name'] = $kardex_signatura_times_name;
                    $output[$i]->form_prescription[$j]['kardex_administer_id'] = $kardex_administer_id;
                    $output[$i]->form_prescription[$j]['kardex_administer_name'] = $kardex_administer_name;

                    $output[$i]->form_prescription[$j]['dose_unit_options'] = options_unit_package($package_id);

                    //------------------- detail form_prescription_parent -//
                    $parent_id = $form_prescription[$j]->prescription_item_type_id; 
                    $parent_used = $form_prescription[$j]->prescription_item_type_used; 
                    $parent_value = $form_prescription[$j]->prescription_item_type_value; 
                    $parent_package = $form_prescription[$j]->prescription_item_type_package; 
                    $parent_signatura_times_id = $form_prescription[$j]->signatura_times_id; 
                    $parent_administer_id = $form_prescription[$j]->administer_id; 
                    $parent_desc = $form_prescription[$j]->desc; 
                    $parent_status_kardex = $form_prescription[$j]->status_kardex; 

                    $dt_form_prescription_parent = array(
                        'id' => $parent_id,
                        'used' => $parent_used,
                        'value' => $parent_value,
                        'package' => $parent_package,
                        'signatura_times_id' => $parent_signatura_times_id,
                        'administer_id' => $parent_administer_id,
                        'desc' => $parent_desc,
                        'status_kardex' => $parent_status_kardex,
                    );

                    if (count($form_prescription_parent) == 0) {
                        array_push($form_prescription_parent, $dt_form_prescription_parent);
                    }

                    $skip = 0;
                    for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                        if ($form_prescription_parent[$ip]['id'] == $parent_id) {
                            $skip = 1;
                        }
                    }

                    if ($skip == 0) {
                        array_push($form_prescription_parent, $dt_form_prescription_parent);
                    }
                    //----------------------------------------------------//
                }

                $output[$i]->form_prescription_parent = $form_prescription_parent;
            } 
        }

        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function registration_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation

        $response_data = array();
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
        if($q_strings['norm'] == '' || $q_strings['norm'] == null) {
            $this->REST_Return(422, 'Norm Kosong!' , $response_data);
            return;
        }

        $select_checks = 'a.register_date,a.code,p.norm,hfi.name as health_facility_item_name,ri.sep,p.name,e.fullname as doctor_in_charge_name';
        // 4C-05: kunjungan difilter branch dari token.
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        $where_checks['a.norm'] = $q_strings['norm'];
        $where_checks['a.institute_branch_id'] = $ph_branch;
        $where_checks['hfi.health_facility_id'] = '40f06534-07ec-4221-ad9d-dea08196079f';
        $join_checks = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
        );
        $order = array('a.code' => 'DESC');
        $output = $this->Registration_val->select(0, $select_checks, $where_checks, $join_checks, $order);
        $response_data = $output;
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * GET Prescription with API
     * -------------------------
     * @method: GET
     */

    public function detail_backup_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        // 4C-05: scope branch fail-closed (objek harus milik branch token).
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        if (!$this->_ph_object_scope($id, $ph_branch, 'read')) { return; }
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            
            $registration_id = '';
            if(isset($q_strings['registration_id'])){
                $registration_id = $q_strings['registration_id']; 
                unset($q_strings['registration_id']);
            }
            $registration_destination_id = '';
            if(isset($q_strings['registration_destination_id'])){
                $registration_destination_id = $q_strings['registration_destination_id']; 
                unset($q_strings['registration_destination_id']);
            }
            if(isset($q_strings['norm'])){
                $norm = $q_strings['norm']; 
                unset($q_strings['norm']);
            }
            $this->$model->whereFields = $q_strings;
            if(isset($q_strings['doctor_in_charge_id'])) $this->$model->whereFields['a.doctor_in_charge_id'] = $q_strings['doctor_in_charge_id']; unset($this->$model->whereFields['doctor_in_charge_id']);
            if($registration_id != ''){
                $this->$model->whereFields['rd.registration_id'] = $registration_id; 
            };
            if($registration_destination_id != ''){
                $this->$model->whereFields['a.registration_destination_id'] = $registration_destination_id; 
            };
            if($norm != ''){
                $this->$model->whereFields['a.norm'] = $norm; 
            };
            $this->$model->whereFields['a.is_delete'] = 0;
            $output = $this->$model->read($id);
            $response_data = array();
            if ($output) {
                for ($i=0; $i < count($output); $i++) { 
                    // $this->Prescription_item->whereFields['a.prescription_item_type_id'] = 1;
                    $this->Prescription_item->whereFields['a.is_delete'] = 0;
                    $form_prescription = $this->Prescription_item->read($output[$i]->id);
                    if ($form_prescription != []) {
                        $form_prescription_parent = array();

                        for ($j=0; $j < count($form_prescription); $j++) { 
                            $pharmacy_prescription_id = $form_prescription[$j]->pharmacy_prescription_id;
                            $pharmacy_catalog_id = $form_prescription[$j]->pharmacy_catalog_id;

                            $select = 'a.id, ki.status, ki.is_change';
                            $where = array(
                                'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
                                'ki.pharmacy_catalog_id' => $pharmacy_catalog_id,
                            );
                            $join = array(
                                array('kardex_item ki', 'a.id = ki.kardex_id', 'INNER'),
                            );
                            $kardex = $this->Kardex->select(0, $select, $where, $join);

                            $kardex_status = $kardex[0]->status;
                            $kardex_is_change = $kardex[0]->is_change;

                            $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                            $output[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                            $output[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                            $output[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $catalog->name;
                            $output[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $catalog->code;
                            $output[$i]->form_prescription[$j]['is_chronic'] = $catalog->is_chronic;
                            $output[$i]->form_prescription[$j]['restriction'] = $catalog->restriction;
                            $output[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                            $output[$i]->form_prescription[$j]['prescription_item_type_package'] = $form_prescription[$j]->prescription_item_type_package;
                            $output[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
                            $output[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
                            $output[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
                            $output[$i]->form_prescription[$j]['dose_name'] = $catalog->dose_name;
                            $output[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                            $output[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
                            $output[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
                            $output[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                            $output[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
                            $output[$i]->form_prescription[$j]['qty_retur'] = $form_prescription[$j]->qty_retur;
                            $output[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                            $output[$i]->form_prescription[$j]['is_retur'] = $form_prescription[$j]->is_retur;
                            $output[$i]->form_prescription[$j]['is_retur_full'] = $form_prescription[$j]->is_retur_full;
                            $output[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
                            $output[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
                            $output[$i]->form_prescription[$j]['var_1'] = $form_prescription[$j]->var_1;
                            $output[$i]->form_prescription[$j]['var_2'] = $form_prescription[$j]->var_2;
                            $output[$i]->form_prescription[$j]['var_3'] = $form_prescription[$j]->var_3;
                            $output[$i]->form_prescription[$j]['var_4'] = $form_prescription[$j]->var_4;
                            $output[$i]->form_prescription[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
                            $output[$i]->form_prescription[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
                            $output[$i]->form_prescription[$j]['pharmacy_catalog_id_retur'] = $form_prescription[$j]->pharmacy_catalog_id_retur;
                            $output[$i]->form_prescription[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
                            $output[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
                            $output[$i]->form_prescription[$j]['kardex_status'] = $kardex_status;
                            $output[$i]->form_prescription[$j]['kardex_is_change'] = $kardex_is_change;

                            //------------------- detail form_prescription_parent -//
                            $parent_id = $form_prescription[$j]->prescription_item_type_id; 
                            $parent_used = $form_prescription[$j]->prescription_item_type_used; 
                            $parent_value = $form_prescription[$j]->prescription_item_type_value; 
                            $parent_package = $form_prescription[$j]->prescription_item_type_package; 

                            $dt_form_prescription_parent = array(
                                'id' => $parent_id,
                                'used' => $parent_used,
                                'value' => $parent_value,
                                'package' => $parent_package,
                            );

                            if (count($form_prescription_parent) == 0) {
                                array_push($form_prescription_parent, $dt_form_prescription_parent);
                            }

                            $skip = 0;
                            for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                                if ($form_prescription_parent[$ip]['id'] == $parent_id) {
                                    $skip = 1;
                                }
                            }

                            if ($skip == 0) {
                                array_push($form_prescription_parent, $dt_form_prescription_parent);
                            }
                            //----------------------------------------------------//
                        }

                        $output[$i]->form_prescription_parent = $form_prescription_parent;
                    } 
                }
            }
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * GET Prescription with API
     * -------------------------
     * @method: GET
     */

    public function detail_concoct_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        // 4C-05: scope branch fail-closed (objek harus milik branch token).
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        if (!$this->_ph_object_scope($id, $ph_branch, 'read')) { return; }
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
        
        $registration_id = '';
        if(isset($q_strings['registration_id'])){
            $registration_id = $q_strings['registration_id']; 
            unset($q_strings['registration_id']);
        }
        $registration_destination_id = '';
        if(isset($q_strings['registration_destination_id'])){
            $registration_destination_id = $q_strings['registration_destination_id']; 
            unset($q_strings['registration_destination_id']);
        }
        $norm = '';
        if(isset($q_strings['norm'])){
            $norm = $q_strings['norm']; 
            unset($q_strings['norm']);
        }
        $this->$model->whereFields = $q_strings;
        if(isset($q_strings['doctor_in_charge_id'])) $this->$model->whereFields['a.doctor_in_charge_id'] = $q_strings['doctor_in_charge_id']; unset($this->$model->whereFields['doctor_in_charge_id']);
        if($registration_id != ''){
            $this->$model->whereFields['rd.registration_id'] = $registration_id; 
        };
        if($registration_destination_id != ''){
            $this->$model->whereFields['a.registration_destination_id'] = $registration_destination_id; 
        };
        if($norm != ''){
            $this->$model->whereFields['a.norm'] = $norm; 
        };
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($id);
        $response_data = array();
        if ($output) {
            for ($i=0; $i < count($output); $i++) { 
                $this->Prescription_item->whereFields['a.is_delete'] = 0;
                $form_prescription = $this->Prescription_item->read($output[$i]->id);
                if ($form_prescription != []) {
                    $done2 = 0;
                    $done3 = 0;
                    $done4 = 0;
                    $done5 = 0;
                    $pres_blank = array();
                    for ($j=0; $j < count($form_prescription); $j++) { 
                        $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                        $resep = array(
                            'pharmacy_prescription_id' => $form_prescription[$j]->pharmacy_prescription_id,
                            'pharmacy_catalog_id' => $form_prescription[$j]->pharmacy_catalog_id,
                            'pharmacy_catalog_name' => $catalog->name,
                            'pharmacy_catalog_code' => $catalog->code,
                            'prescription_item_type_id' => $form_prescription[$j]->prescription_item_type_id,
                            'prescription_item_type_name' => $form_prescription[$j]->prescription_item_type_id == 1 ? 'OBAT SATUAN' : 'RACIK - '.($form_prescription[$j]->prescription_item_type_id - 1) ,
                            'prescription_item_type_used' => $form_prescription[$j]->prescription_item_type_used,
                            'prescription_item_type_val' => $form_prescription[$j]->prescription_item_type_value,
                            'package_id' => $form_prescription[$j]->package_id,
                            'package_name' => $form_prescription[$j]->package_name,
                            'signatura_times_id' => $form_prescription[$j]->signatura_times_id,
                            'administer_id' => $form_prescription[$j]->administer_id,
                            'dose' => $form_prescription[$j]->dose,
                            'dose_name' => $catalog->dose_name,
                            'dose_unit_id' => $form_prescription[$j]->dose_unit_id,
                            'dose_doctor' => $form_prescription[$j]->dose_doctor,
                            'batch' => $form_prescription[$j]->batch,
                            'expired' => $form_prescription[$j]->expired,
                            'qty_doctor' => $form_prescription[$j]->qty_doctor,
                            'qty' => $form_prescription[$j]->qty,
                            'qty_retur' => $form_prescription[$j]->qty_retur,
                            'qty_copy' => $form_prescription[$j]->qty_copy,
                            'is_retur' => $form_prescription[$j]->is_retur,
                            'is_retur_full' => $form_prescription[$j]->is_retur_full,
                            'used' => $form_prescription[$j]->used ? $form_prescription[$j]->used : '',
                            'desc' => $form_prescription[$j]->desc ? $form_prescription[$j]->desc : '',
                            'var_1' => $form_prescription[$j]->price*1.3,
                            'var_2' => $form_prescription[$j]->qty*($form_prescription[$j]->price*1.3),
                            'var_3' => $form_prescription[$j]->var_3,
                            'var_4' => $form_prescription[$j]->var_4,
                            'doctor_in_charge_id' => $form_prescription[$j]->doctor_in_charge_id,
                            'pharmacy_catalog_id_ref' => $form_prescription[$j]->pharmacy_catalog_id_ref,
                            'pharmacy_catalog_id_retur' => $form_prescription[$j]->pharmacy_catalog_id_retur,
                            'pharmacy_catalog_id_sub' => $form_prescription[$j]->pharmacy_catalog_id_sub,
                            'sort' => $form_prescription[$j]->sort,
                        );
                        if ($form_prescription[$j]->prescription_item_type_id == 1) {
                            array_push($pres_blank, $resep);
                        } else if ($form_prescription[$j]->prescription_item_type_id == 2) {
                            if ($done2 == 1) {
                                continue;
                            }
                            array_push($pres_blank, $resep);
                            $done2 = 1;
                        } else if ($form_prescription[$j]->prescription_item_type_id == 3) {
                            if ($done3 == 1) {  
                                continue;
                            }
                            array_push($pres_blank, $resep);
                            $done3 = 1;
                        } else if ($form_prescription[$j]->prescription_item_type_id == 4) {
                            if ($done4 == 1) {
                                continue;
                            }
                            array_push($pres_blank, $resep);
                            $done4 = 1;
                        } else if ($form_prescription[$j]->prescription_item_type_id == 5) {
                            if ($done5 == 1) {
                                continue;
                            }
                            array_push($pres_blank, $resep);
                            $done5 = 1;
                        }
                        $output[$i]->form_prescription = $pres_blank;
                            // $output[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                            // $output[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                            // $output[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $catalog->name;
                            // $output[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $catalog->code;
                            // $output[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                            // $output[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
                            // $output[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
                            // $output[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
                            // $output[$i]->form_prescription[$j]['dose_name'] = $catalog->dose_name;
                            // $output[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                            // $output[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
                            // $output[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
                            // $output[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                            // $output[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
                            // $output[$i]->form_prescription[$j]['qty_retur'] = $form_prescription[$j]->qty_retur;
                            // $output[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                            // $output[$i]->form_prescription[$j]['is_retur'] = $form_prescription[$j]->is_retur;
                            // $output[$i]->form_prescription[$j]['is_retur_full'] = $form_prescription[$j]->is_retur_full;
                            // $output[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
                            // $output[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
                            // $output[$i]->form_prescription[$j]['var_1'] = $form_prescription[$j]->price*1.3;
                            // $output[$i]->form_prescription[$j]['var_2'] = $form_prescription[$j]->qty*($form_prescription[$j]->price*1.3);
                            // $output[$i]->form_prescription[$j]['var_3'] = $form_prescription[$j]->var_3;
                            // $output[$i]->form_prescription[$j]['var_4'] = $form_prescription[$j]->var_4;
                            // $output[$i]->form_prescription[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
                            // $output[$i]->form_prescription[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
                            // $output[$i]->form_prescription[$j]['pharmacy_catalog_id_retur'] = $form_prescription[$j]->pharmacy_catalog_id_retur;
                            // $output[$i]->form_prescription[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
                            // $output[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
                            // $done = 1;
                    }
                } 
            }
        }
        if ($output != [] && $id != 0) {
            $response_data = $output;
            $error   = false;
        } else if($output != [] or $id == 0) {
            $response_data = $output;
            $error   = false;
        } else {
            $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
            return;
        }
        if ($error == true) {
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            $this->REST_Return(200, 'SUCCESS', $response_data);
        }
        return;
    }

    public function billing_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        // 4C-05: scope branch fail-closed (objek harus milik branch token).
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        if (!$this->_ph_object_scope($id, $ph_branch, 'read')) { return; }
        $model  = "Prescription_short";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            

            $registration_id = $q_strings['registration_id'] ?? null;

            if ($registration_id == null) {
                $this->REST_Return(400, 'registration_id is required');
                return;
            }

            $output = $this->db->query(
            "
                -- Case 1: a.registration_destination_id is NOT NULL
                SELECT a.*, a.id as registration_destination_id
                FROM pharmacy_prescription AS a
                LEFT JOIN soap_p sp ON a.soap_p_id = sp.id
                LEFT JOIN soap s ON sp.soap_id = s.id
                JOIN registration_destination rd ON a.registration_destination_id = rd.id
                WHERE rd.registration_id = '$registration_id'
                AND a.flag = 1
                AND a.is_delete = 0

                UNION ALL

                -- Case 2: a.registration_destination_id IS NULL, use soap.registration_destination_id
                SELECT a.*, a.id as registration_destination_id
                FROM pharmacy_prescription AS a
                LEFT JOIN soap_p sp ON a.soap_p_id = sp.id
                LEFT JOIN soap s ON sp.soap_id = s.id
                JOIN registration_destination rd ON s.registration_destination_id = rd.id
                WHERE (a.registration_destination_id IS NULL OR a.registration_destination_id = '') 
                AND rd.registration_id = '$registration_id'
                AND a.flag = 1
                AND a.is_delete = 0
            "
            )->result();

		    $output = $this->convert_data($output);

            // $this->$model->whereFields['a.flag'] = 1; 
            // $this->$model->whereFields['a.is_delete'] = 0; 
            // $output = $this->$model->read($id);
            
            $response_data = array();
            if ($output) {
                for ($i=0; $i < count($output); $i++) { 
                    $this->Prescription_item->whereFields['a.is_delete'] = 0;
                    if ($output[$i]->id) {
                        $form_prescription = $this->Prescription_item->read($output[$i]->id);
                        if ($form_prescription != []) {
                            $total_var_1 = 0;
                            for ($j=0; $j < count($form_prescription); $j++) { 
                                $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                                $output[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                                $output[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                                $output[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $catalog->name;
                                $output[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $catalog->code;
                                $output[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                                $output[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
                                $output[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
                                $output[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
                                $output[$i]->form_prescription[$j]['dose_name'] = $catalog->dose_name;
                                $output[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                                $output[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
                                $output[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
                                $output[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                                $output[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
                                $output[$i]->form_prescription[$j]['qty_retur'] = $form_prescription[$j]->qty_retur;
                                $output[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                                $output[$i]->form_prescription[$j]['is_retur'] = $form_prescription[$j]->is_retur;
                                $output[$i]->form_prescription[$j]['is_retur_full'] = $form_prescription[$j]->is_retur_full;
                                $output[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
                                $output[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
                                $output[$i]->form_prescription[$j]['var_1'] = $form_prescription[$j]->var_1;
                                // P0-4: nilai baris = (qty_awal - qty_retur) x harga satuan. Dihitung
                                // server dari kolom histori; var_2 tersimpan tidak dipakai lagi.
                                $output[$i]->form_prescription[$j]['var_2'] = round(max(0, (float)$form_prescription[$j]->qty - (float)$form_prescription[$j]->qty_retur) * (float)$form_prescription[$j]->var_1, 2);
                                $output[$i]->form_prescription[$j]['var_3'] = $form_prescription[$j]->var_3;
                                $output[$i]->form_prescription[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
                                $output[$i]->form_prescription[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
                                $output[$i]->form_prescription[$j]['pharmacy_catalog_id_retur'] = $form_prescription[$j]->pharmacy_catalog_id_retur;
                                $output[$i]->form_prescription[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
                                $output[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
                                $total_var_1 += round(max(0, (float)$form_prescription[$j]->qty - (float)$form_prescription[$j]->qty_retur) * (float)$form_prescription[$j]->var_1, 2);
                            }
                            $output[$i]->var_1 = $total_var_1;
                        } 
                    }
                }
            }
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

	public function convert_data($data) {
		// convert numeric types
		for ($i=0; $i < count(($data)); $i++) { 
			foreach ($data[$i] as $key => $val) {
				if($key == 'phone') {
					continue;
				}
				if($key == 'norm') {
					continue;
				}
				if($key == 'code_booking') {
					continue;
				}
				if($key == 'code_booking') {
					continue;
				}
				if($key == 'no_invoice') {
					continue;
				}
				if($key == 'code_s') {
					continue;
				}
				// if($key == 'code') {
				// 	continue;
				// }
				if($key == 'number_insurance') {
					continue;
				}
				if(is_numeric($val)) {
					if(fmod($val,1) !== 0.0) {
						// echo "ya";
						$data[$i]->$key = floatval($val);
					} else {
						// echo "no";
						$data[$i]->$key = intval($val);
					}
				}
			}
		}
		return $data;
	}

    // public function billing_get($p = 0) {
    //     header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = "Prescription_short";

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    //         // if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
    //         // if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            
    //         $registration_id = '';
    //         if(isset($q_strings['registration_id'])){
    //             $registration_id = $q_strings['registration_id']; 
    //         }
    //         $this->Registration->whereFields = $q_strings;
    //         // if($registration_id != ''){
    //         //     $this->$model->whereFields['rd.registration_id'] = $registration_id; 
    //         // };
    //         // $this->$model->whereFields['a.flag'] = 1; 
    //         // $this->$model->whereFields['a.is_delete'] = 0; 
    //         // $output = $this->$model->read($id);

    //         $where['a.id'] = $registration_id;
    
    //         $select = 'pres.no_order,coalesce(pres.id,pres_two.id) as id,coalesce(pres.norm,pres_two.norm) as norm,coalesce(pres.no_order,pres_two.no_order) as no_order,coalesce(pres.flag,pres_two.flag) as flag,coalesce(pres.var_1,pres_two.var_1) as var_1,coalesce(pres.var_2,pres_two.var_2) as var_2,,coalesce(pres.var_3,pres_two.var_3) as var_3,coalesce(pres.is_retur,pres_two.is_retur) as is_retur,coalesce(pres.type_free_id,pres_two.type_free_id) as type_free_id,coalesce(pres.date_added,pres_two.date_added) as date_added';
    //         $join = array(
    //             array('registration_destination rd', 'a.id = rd.registration_id', 'LEFT'),
	// 	        array('soap s', 'rd.id = s.registration_destination_id', 'LEFT'),
	// 	        array('soap_p sp', 's.id = sp.soap_id', 'LEFT'),
    //             array('pharmacy_prescription pres', 'sp.id = pres.soap_p_id', 'LEFT'),
    //             array('pharmacy_prescription pres_two', 'rd.id = pres_two.registration_destination_id', 'LEFT'),
    //         );
    //         $group = array();
    //         $group = array('coalesce(pres.id,pres_two.id)');
    //         $output = $this->Registration->select(0, $select, $where, $join,array(),$group);
    //         $response_data = array();
    //         if ($output) {
    //             for ($i=0; $i < count($output); $i++) { 
    //                 $this->Prescription_item->whereFields['a.is_delete'] = 0;
    //                 if ($output[$i]->id) {
    //                     $form_prescription = $this->Prescription_item->read($output[$i]->id);
    //                     if ($form_prescription != []) {
    //                         for ($j=0; $j < count($form_prescription); $j++) { 
    //                             $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
    //                             $output[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
    //                             $output[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
    //                             $output[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $catalog->name;
    //                             $output[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $catalog->code;
    //                             $output[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
    //                             $output[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
    //                             $output[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
    //                             $output[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
    //                             $output[$i]->form_prescription[$j]['dose_name'] = $catalog->dose_name;
    //                             $output[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
    //                             $output[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
    //                             $output[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
    //                             $output[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
    //                             $output[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
    //                             $output[$i]->form_prescription[$j]['qty_retur'] = $form_prescription[$j]->qty_retur;
    //                             $output[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
    //                             $output[$i]->form_prescription[$j]['is_retur'] = $form_prescription[$j]->is_retur;
    //                             $output[$i]->form_prescription[$j]['is_retur_full'] = $form_prescription[$j]->is_retur_full;
    //                             $output[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
    //                             $output[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
    //                             $output[$i]->form_prescription[$j]['var_1'] = $form_prescription[$j]->var_1;
    //                             $output[$i]->form_prescription[$j]['var_2'] = $form_prescription[$j]->qty*($form_prescription[$j]->var_1);
    //                             $output[$i]->form_prescription[$j]['var_3'] = $form_prescription[$j]->var_3;
    //                             $output[$i]->form_prescription[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
    //                             $output[$i]->form_prescription[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
    //                             $output[$i]->form_prescription[$j]['pharmacy_catalog_id_retur'] = $form_prescription[$j]->pharmacy_catalog_id_retur;
    //                             $output[$i]->form_prescription[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
    //                             $output[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
    //                         }
    //                     } 
    //                 }
    //             }
    //         }
    //         if ($output != [] && $id != 0) {
    //             $response_data = $output;
    //             $error   = false;
    //         } else if($output != [] or $id == 0) {
    //             $response_data = $output;
    //             $error   = false;
    //         } else {
    //             $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
    //             return;
    //         }
    //         if ($error == true) {
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             $this->REST_Return(200, 'SUCCESS', $response_data);
    //         }
    //         return;
    //     } else {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
    // }

    public function item_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        // 4C-05: scope branch fail-closed (objek harus milik branch token).
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        if (!$this->_ph_object_scope($id, $ph_branch, 'read')) { return; }
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields = $q_strings;
            $output = $this->$model->read($id);
            $response_data = array();
            $item = array();
            if ($output) {
                $this->Prescription_item->whereFields['a.is_delete'] = 0;
                $form_prescription = $this->Prescription_item->read($output[0]->id);
                if ($form_prescription != []) {
                    for ($j=0; $j < count($form_prescription); $j++) { 
                        $pharmacy_catalog_id = $form_prescription[$j]->pharmacy_catalog_id;

                        $kardex_status = null;
                        $kardex_is_change = null;
                        $kardex_dose_unit_id = null;
                        $kardex_dose_unit_name = null;
                        $kardex_signatura_times_id = null;
                        $kardex_signatura_times_name = null;
                        $kardex_administer_id = null;
                        $kardex_administer_name = null;

                        $select = 'a.id, ki.dose_unit_id, u.name as dose_unit_name, ki.signatura_times_id, st.name as signatura_times_name, ki.administer_id, pci.name as administer_name, ki.status, ki.is_change';
                        $where = array(
                            'a.pharmacy_prescription_id' => $id,
                            'ki.pharmacy_catalog_id' => $pharmacy_catalog_id,
                        );
                        $join = array(
                            array('kardex_item ki', 'a.id = ki.kardex_id', 'INNER'),
                            array('unit u', 'ki.dose_unit_id = u.id', 'INNER'),
                            array('signatura_times st', 'ki.signatura_times_id = st.id', 'INNER'),
                            array('pharmacy_catalog_instruction pci', 'ki.administer_id = pci.id', 'INNER'),
                        );
                        $kardex = $this->Kardex->select(0, $select, $where, $join);
                        if (count($kardex) > 0) {
                            $kardex_status = $kardex[0]->status;
                            $kardex_is_change = $kardex[0]->is_change;
                            $kardex_dose_unit_id = $kardex[0]->dose_unit_id;
                            $kardex_dose_unit_name = $kardex[0]->dose_unit_name;
                            $kardex_signatura_times_id = $kardex[0]->signatura_times_id;
                            $kardex_signatura_times_name = $kardex[0]->signatura_times_name;
                            $kardex_administer_id = $kardex[0]->administer_id;
                            $kardex_administer_name = $kardex[0]->administer_name;
                        }

                        if ($form_prescription[$j]->pharmacy_catalog_id_sub) {
                            $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id_sub,false)[0];
                        } else {
                            $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                        }
                        $this->Assets->whereFields['a.pharmacy_catalog_id'] = $catalog->id;
                        $this->Assets->whereFields['a.pharmacy_depo_id'] = $output[0]->pharmacy_depo_id;
                        $stock = $this->Assets->read(0,false)[0];
                        $this->Assets_price->whereFields['a.pharmacy_catalog_id'] = $catalog->id;
                        $this->Assets_price->whereFields['a.is_active'] = 1;
                        $price = $this->Assets_price->read(0,false)[0];
                        $item[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                        $item[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                        $item[$j]['pharmacy_catalog_name'] = $catalog->name;
                        $item[$j]['pharmacy_catalog_code'] = $catalog->code;
                        $item[$j]['pharmacy_catalog_dose'] = $catalog->dose;
                        $item[$j]['pharmacy_catalog_dose_unit_id'] = $catalog->dose_unit_id;
                        $item[$j]['pharmacy_catalog_dose_unit_name'] = $catalog->dose_unit_name;
                        $item[$j]['pharmacy_catalog_packaging_value'] = $catalog->packaging_value;
                        $item[$j]['pharmacy_catalog_packaging_unit_id'] = $catalog->packaging_unit_id;
                        $item[$j]['pharmacy_catalog_packaging_unit_name'] = $catalog->packaging_unit_name;
                        $item[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                        $item[$j]['package_id'] = $form_prescription[$j]->package_id;
                        $item[$j]['package_name'] = $form_prescription[$j]->package_name;
                        $item[$j]['signatura_times_id'] = $form_prescription[$j]->signatura_times_id;
                        $item[$j]['signatura_times_name'] = $form_prescription[$j]->signatura_times_name;
                        $item[$j]['administer_id'] = $form_prescription[$j]->administer_id;
                        $item[$j]['administer_name'] = $form_prescription[$j]->administer_name;
                        $item[$j]['dose'] = $form_prescription[$j]->dose;
                        $item[$j]['dose_unit_id'] = $form_prescription[$j]->dose_unit_id;
                        $item[$j]['dose_unit_name'] = $form_prescription[$j]->dose_unit_name;
                        $item[$j]['dose_name'] = $catalog->dose_name;
                        $item[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                        $item[$j]['batch'] = $form_prescription[$j]->batch;
                        $item[$j]['expired'] = $form_prescription[$j]->expired;
                        $item[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                        $item[$j]['stock'] = $stock->admin_amount;
                        $item[$j]['hja'] = $price->hja_item;
                        $item[$j]['hp'] = $price->hp_item;
                        $item[$j]['hpp'] = $price->hpp_item;
                        $item[$j]['hna'] = $price->hna_item;
                        $item[$j]['qty'] = $form_prescription[$j]->qty;
                        $item[$j]['qty_retur'] = $form_prescription[$j]->qty_retur;
                        $item[$j]['is_retur'] = $form_prescription[$j]->is_retur;
                        $item[$j]['is_retur_full'] = $form_prescription[$j]->is_retur_full;
                        // 4C-04: status retur (pending/approved/rejected) untuk form retur FE.
                        $item[$j]['retur_status'] = isset($form_prescription[$j]->retur_status) ? $form_prescription[$j]->retur_status : null;
                        $item[$j]['retur_qty_requested'] = isset($form_prescription[$j]->retur_qty_requested) ? $form_prescription[$j]->retur_qty_requested : null;
                        $item[$j]['retur_reason'] = isset($form_prescription[$j]->retur_reason) ? $form_prescription[$j]->retur_reason : null;
                        $item[$j]['retur_requested_at'] = isset($form_prescription[$j]->retur_requested_at) ? $form_prescription[$j]->retur_requested_at : null;
                        $item[$j]['retur_approved_by'] = isset($form_prescription[$j]->retur_approved_by) ? $form_prescription[$j]->retur_approved_by : null;
                        $item[$j]['retur_approved_at'] = isset($form_prescription[$j]->retur_approved_at) ? $form_prescription[$j]->retur_approved_at : null;
                        $item[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                        $item[$j]['used'] = $form_prescription[$j]->used;
                        $item[$j]['desc'] = $form_prescription[$j]->desc;
                        $item[$j]['var_1'] = $form_prescription[$j]->var_1;
                        $item[$j]['var_2'] = $form_prescription[$j]->var_2;
                        $item[$j]['var_3'] = $form_prescription[$j]->var_3;
                        $item[$j]['var_4'] = $form_prescription[$j]->var_4;
                        $item[$j]['is_chronic'] = $catalog->is_chronic;
                        $item[$j]['restriction'] = $catalog->restriction;
                        $item[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
                        $item[$j]['prescription_item_type_used'] = $form_prescription[$j]->prescription_item_type_used;
                        $item[$j]['prescription_item_type_value'] = $form_prescription[$j]->prescription_item_type_value;
                        $item[$j]['prescription_item_type_package'] = $form_prescription[$j]->prescription_item_type_package;
                        $item[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
                        $item[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
                        $item[$j]['pharmacy_catalog_id_retur'] = $form_prescription[$j]->pharmacy_catalog_id_retur;
                        $item[$j]['sort'] = $form_prescription[$j]->sort;
                        $item[$j]['kardex_status'] = $kardex_status;
                        $item[$j]['kardex_is_change'] = $kardex_is_change;
                        $item[$j]['kardex_dose_unit_id'] = $kardex_dose_unit_id;
                        $item[$j]['kardex_dose_unit_name'] = $kardex_dose_unit_name;
                        $item[$j]['kardex_signatura_times_id'] = $kardex_signatura_times_id;
                        $item[$j]['kardex_signatura_times_name'] = $kardex_signatura_times_name;
                        $item[$j]['kardex_administer_id'] = $kardex_administer_id;
                        $item[$j]['kardex_administer_name'] = $kardex_administer_name;
                    }
                    $output = $item;
                }
            }
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function catalog_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            // 4C-05: depo & katalog harus di dalam branch token.
            if (trim((string) $institute_branch_id) === '') {
                $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
                return;
            }
            if (!$this->_ph_depo_scope($q_strings['pharmacy_depo_id'], trim((string) $institute_branch_id), 'read')) { return; }
            $catalog = $this->Catalog->read($p,false)[0];
            if (trim((string) $catalog->institute_branch_id) !== '' && (string) $catalog->institute_branch_id !== trim((string) $institute_branch_id)) {
                $this->_ph_deny('PHARMACY_CATALOG_SCOPE_MISMATCH', null, isset($this->s1_actor['user_id']) ? $this->s1_actor['user_id'] : null, 'read',
                    array('reason' => 'catalog_branch_mismatch', 'pharmacy_catalog_id' => $p,
                          'catalog_branch' => $catalog->institute_branch_id, 'token_branch' => trim((string) $institute_branch_id)), 403,
                    'Katalog berada di luar branch scope.');
                return;
            }
            $this->Assets->whereFields['a.pharmacy_catalog_id'] = $q_strings['id'];
            $this->Assets->whereFields['a.pharmacy_depo_id'] = $q_strings['pharmacy_depo_id'];
            $stock = $this->Assets->read(0,false)[0];
            $this->Assets_price->whereFields['a.pharmacy_catalog_id'] = $q_strings['id'];
            $this->Assets_price->whereFields['a.is_active'] = 1;
            $price = $this->Assets_price->read(0,false)[0];
            $output['pharmacy_catalog_id'] = $p;
            $output['prescription_item_type_id'] = 1;
            $output['batch'] = $stock->batch;
            $output['expired'] = $stock->expired;
            $output['stock'] = $stock->admin_amount;
            $output['hja'] = $price->hja_item;
            $output['hp'] = $price->hp_item;
            $output['hpp'] = $price->hpp_item;
            $output['hna'] = $price->hna_item;
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function catalog_mutasi_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            // 4C-05: depo & katalog harus di dalam branch token.
            if (trim((string) $institute_branch_id) === '') {
                $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
                return;
            }
            if (!$this->_ph_depo_scope($q_strings['pharmacy_depo_id'], trim((string) $institute_branch_id), 'read')) { return; }
            $catalog = $this->Catalog->read($p,false)[0];
            if (trim((string) $catalog->institute_branch_id) !== '' && (string) $catalog->institute_branch_id !== trim((string) $institute_branch_id)) {
                $this->_ph_deny('PHARMACY_CATALOG_SCOPE_MISMATCH', null, isset($this->s1_actor['user_id']) ? $this->s1_actor['user_id'] : null, 'read',
                    array('reason' => 'catalog_branch_mismatch', 'pharmacy_catalog_id' => $p,
                          'catalog_branch' => $catalog->institute_branch_id, 'token_branch' => trim((string) $institute_branch_id)), 403,
                    'Katalog berada di luar branch scope.');
                return;
            }
            $this->Assets->whereFields['a.pharmacy_catalog_id'] = $q_strings['id'];
            $this->Assets->whereFields['a.pharmacy_depo_id'] = $q_strings['pharmacy_depo_id'];
            $stock = $this->Assets->read(0,false)[0];
            $output['pharmacy_catalog_id'] = $q_strings['id'];
            $output['batch'] = $stock->batch;
            $output['expired'] = $stock->expired;
            $output['stock'] = $stock->admin_amount;

            $CatalogData = $this->Catalog->read($q_strings['id'],false)[0];
            $output['code'] = $CatalogData->code;
            $output['name'] = $CatalogData->name;
            $output['name_brand'] = $CatalogData->name_brand;
            $output['brand_id'] = $CatalogData->brand_id;
            $output['package_id'] = $CatalogData->package_id;
            $output['package_name'] = $CatalogData->package_name;
            $output['value_package'] = $CatalogData->value_package;
            $output['package_big_id'] = $CatalogData->package_big_id;
            $output['package_big_name'] = $CatalogData->package_big_name;
            $output['value_package_big'] = $CatalogData->value_package_big;
            $output['suppliers_id'] = $CatalogData->suppliers_id;
            $output['factory_id'] = $CatalogData->factory_id;
            $output['composition'] = $CatalogData->composition;
            $output['dose_id'] = $CatalogData->dose_id;
            $output['dose'] = $CatalogData->dose;
            $output['min'] = $CatalogData->min;
            $output['max'] = $CatalogData->max;
            $output['is_delete'] = $CatalogData->is_delete;
            $output['date_added'] = $CatalogData->date_added;
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function list_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            // $this->$model->whereLike = $q_strings;
            // 4C-05: filter branch dari token DIAKTIFKAN (sebelumnya dikomentari => lintas branch).
            if (trim((string) $institute_branch_id) === '') {
                $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
                return;
            }
            $this->$model->whereFields['a.institute_branch_id'] = trim((string) $institute_branch_id);
            // $this->$model->whereFields['a.id'] = 'ea1c81a3-73a5-468c-9681-8ed86ca1a572';
            // $q_strings['date_start'] = '2024-08-23';
            // $q_strings['date_end'] = '2024-08-23';
            if(isset($q_strings['patient_name'])) {
                $this->$model->whereLike['p.name'] = $q_strings['patient_name'];
                unset($q_strings['patient_name']);
            }
            if(isset($q_strings['norm'])) {
                $this->$model->whereFields['p.norm'] = $q_strings['norm'];
                unset($q_strings['norm']);
            }
            if(isset($q_strings['fullname'])) {
                $this->$model->whereFields['a.fullname'] = $q_strings['fullname'];
                unset($q_strings['fullname']);
            }
            if(isset($q_strings['type_prescription_id'])) {
                $this->$model->whereFields['a.type_prescription_id'] = $q_strings['type_prescription_id'];
                unset($q_strings['type_prescription_id']);
            }
            if(isset($q_strings['flag'])) {
                $this->$model->whereFields['a.flag'] = $q_strings['flag'];
                unset($q_strings['flag']);
            }
            if(isset($q_strings['farmasi'])) {
                $farmasi = $q_strings['farmasi'];
                if ($farmasi == 'poliklinik') {
                    $this->$model->whereFields['hfi.health_facility_id'] = '3cb626f5-9148-4fb1-8663-3d7f028b01a6';
                }
                if ($farmasi == 'bebas') {
                    $this->$model->whereFields['a.type_prescription_id'] = 5;
                }
                if ($farmasi == 'ranap') {
                    $this->$model->whereFields['hfi.health_facility_id'] = '40f06534-07ec-4221-ad9d-dea08196079f';
                }
                if ($farmasi == 'igd') {
                    $this->$model->whereFields['a.pharmacy_depo_id'] = '4105d8bc-5769-463e-bc67-aae2fc5adf10';
                }
                if ($farmasi == 'ok') {
                    $this->$model->whereFields['a.pharmacy_depo_id'] = '822d71e8-7a2b-4fc6-a372-40a6b458d8c3';
                }
                unset($q_strings['farmasi']);
            }
            if(isset($q_strings['pharmacy_depo_id'])) {
                $this->$model->whereFields['a.pharmacy_depo_id'] = $q_strings['pharmacy_depo_id'];
                unset($q_strings['pharmacy_depo_id']);
            }
            if(isset($q_strings['date_start']) && isset($q_strings['date_start'])) {
                $this->$model->whereFields['a.date_added >='] = $q_strings['date_start'].' 00:00:00';
                $this->$model->whereFields['a.date_added <='] = $q_strings['date_start'].' 23:59:59';
            } else {
                $q_strings['date_start'] = date('Y-m-d');
                $q_strings['date_start'] = date('Y-m-d');
                $this->$model->whereFields['a.date_added >='] = $q_strings['date_start'].' 00:00:00';
                $this->$model->whereFields['a.date_added <='] = $q_strings['date_start'].' 23:59:59';
            }
            $this->$model->whereFields['a.is_delete'] = 0;
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function queue_get($p = 0){
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

        if (!isset($q_strings['date'])) {
            $this->REST_Return(400, 'date is required');
            return;
        }

        $code_prefix = 'PRJ';

        $date = $q_strings['date'];

        $date_start = $date . ' 00:00:00';
        $date_end = $date . ' 23:59:59';

        // 4C-05: antrean farmasi difilter branch dari token.
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        $select_prj = 'a.no_order, a.queue';
        $where_prj = array(
            'a.is_delete' => 0,
            'a.institute_branch_id' => $ph_branch,
            'a.date_added >=' => $date_start,
            'a.date_added <=' => $date_end,
        );
        $join_prj = array();
        $order_prj = array('a.queue' => 'ASC');
        $this->$model->whereLike['a.no_order'] = $code_prefix;
        $prescription = $this->$model->select(0, $select_prj, $where_prj, $join_prj, $order_prj);

        $data_queue = [];

        for ($i=0; $i < count($prescription); $i++) { 
            $queue_number = $prescription[$i]->queue;
            array_push($data_queue, $queue_number);
        }

        $select_queue = 'a.name, a.data, a.status';
        $where_queue = array(
            'a.name' => $code_prefix,
            'a.date' => $date,
        );
        $join_queue = array();
        $order_queue = array('a.status' => 'ASC');
        $queue = $this->Queue_display->select(0, $select_queue, $where_queue, $join_queue, $order_queue);

        $this->db->trans_start();

        if (count($queue) == 0) {
            $select = 'a.code, a.name, a.status';
            $where = array(
                'a.code' => $code_prefix,
                'a.is_delete' => 0
            );
            $queue_status = $this->Queue_status->select(0, $select, $where);

            $dt_queue = array(
                'name' => $code_prefix,
                'data' => implode(',', $data_queue),
                'date' => $date,
                'date_added' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            );

            for ($i=0; $i < count($queue_status); $i++) { 
                $queue_status_num = $queue_status[$i]->status;

                $dt_queue['status'] = $queue_status_num;

                if ($i != 1) {
                    $dt_queue['data'] = '';
                }

                $this->Queue_display->replace($dt_queue);
            }

            $queue = $this->Queue_display->select(0, $select_queue, $where_queue);
        }

        $queue_data_status_one = ($queue[0]->data == '') ? [] : explode(',', $queue[0]->data);

        for ($i=0; $i < count($data_queue); $i++) { 
            $queue_number = $data_queue[$i];

            $add = 1;
            for ($j=0; $j < count($queue); $j++) { 
                $queue_data = ($queue[$j]->data == '') ? [] : explode(',', $queue[$j]->data);

                if (in_array($queue_number, $queue_data) == 1) {
                    $add = 0;
                }
            }

            if ($add == 1) {
                array_push($queue_data_status_one, $queue_number);
            }
        }

        $dt_queue = array(
            'name' => $code_prefix,
            'data' => implode(',', $queue_data_status_one),
            'date' => $date,
            'status' => 1,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $this->Queue_display->replace($dt_queue);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to get queue', $response_data);
            return;
        }

        $queue = $this->Queue_display->select(0, $select_queue, $where_queue);

        for ($i=0; $i < count($queue); $i++) { 
            $status = $queue[$i]->status;
            $queue_number_arr = ($queue[$i]->data == '') ? [] : explode(',', $queue[$i]->data);
            $output_data = array();

            for ($j=0; $j < count($queue_number_arr); $j++) { 
                $queue_number = $queue_number_arr[$j];

                $select_prj = 'a.no_order, a.queue, rd.code_queue as registration_destination_code_queue, rd.queue as registration_destination_queue';
                $where_prj = array(
                    'a.is_delete' => 0,
                    'a.queue' => $queue_number,
                    'a.date_added >=' => $date_start,
                    'a.date_added <=' => $date_end,
                );
                $join_prj = array(
                    // array('patient ptn', 'a.norm = ptn.norm', 'INNER'),
                    array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
                    array('soap s', 'sp.soap_id = s.id', 'LEFT'),
                    array('registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'),
                );
                $this->$model->whereLike['a.no_order'] = $code_prefix;
                $prescription = $this->$model->select(0, $select_prj, $where_prj, $join_prj);

                $no_order = $prescription[0]->no_order;
                // $patient_name = $prescription[0]->patient_name;
                $registration_destination_code_queue = $prescription[0]->registration_destination_code_queue;
                $registration_destination_queue = $prescription[0]->registration_destination_queue;

                $dt_output = array(
                    'queue' => $queue_number,
                    'no_order' => $no_order,
                    'registration_destination_queue' => $registration_destination_code_queue . '-' . $registration_destination_queue,
                    // 'patient_name' => $patient_name,
                );

                array_push($output_data, $dt_output);
            }

            $select = 'a.name';
            $where = array(
                'a.code' => $code_prefix,
                'a.status' => $status,
                'a.is_delete' => 0,
            );
            $queue_status = $this->Queue_status->select(0, $select, $where);

            $queue_status_name = $queue_status[0]->name;

            $queue[$i]->data = $output_data;
            $queue[$i]->name = $queue_status_name;
        }

        $this->REST_Return(200, 'SUCCESS', $queue);
        return;
    }

    public function queue_next_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->queue_next_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $queue_number = $id;
    
        if ($queue_number === 0 || $queue_number === "0") {
            $this->REST_Return(400, 'queue_number is required');
            return;
        }

        $date = $dt_put['date'];

        $select_queue = 'a.name, a.data, a.status';
        $where_queue = array(
            'a.name' => 'PRJ',
            'a.date' => $date,
        );
        $queue = $this->Queue_display->select(0, $select_queue, $where_queue);

        /* Start Transaction */
        $this->db->trans_start();

        $updated_status = 0;
        for ($i=0; $i < count($queue); $i++) { 
            $queue_data = explode(',', $queue[$i]->data);
            $queue_status = $queue[$i]->status;

            if (in_array($queue_number, $queue_data) == 1) {
                $idx_remove = array_search($queue_number, $queue_data);
                unset($queue_data[$idx_remove]);

                $updated_status = $queue_status + 1;

                if ($updated_status > count($queue)) {
                    $this->REST_Return(409, 'queue number ' . $queue_number . ' is in the last step');
                    return;
                }

                $dt_queue = array(
                    'name' => 'PRJ',
                    'data' => implode(',', $queue_data),
                    'date' => $date,
                    'status' => $queue_status,
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Queue_display->replace($dt_queue);
            }
        }

        if ($updated_status == 0) {
            $this->REST_Return(404, 'there is no queue number ' . $queue_number . ' in queue');
            return;
        }

        for ($i=0; $i < count($queue); $i++) { 
            $queue_data = ($queue[$i]->data == '') ? [] : explode(',', $queue[$i]->data);
            $queue_status = $queue[$i]->status;

            if ($updated_status == $queue_status) {
                array_push($queue_data, $queue_number);

                $dt_queue = array(
                    'name' => 'PRJ',
                    'data' => implode(',', $queue_data),
                    'date' => $date,
                    'status' => $queue_status,
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Queue_display->replace($dt_queue);
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to queue next', $response_data);
            return;
        }
    
        $response_data = array(
            'queue_number'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function queue_back_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->queue_next_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $queue_number = $id;
    
        if ($queue_number === 0 || $queue_number === "0") {
            $this->REST_Return(400, 'queue_number is required');
            return;
        }

        $date = $dt_put['date'];

        $select_queue = 'a.name, a.data, a.status';
        $where_queue = array(
            'a.name' => 'PRJ',
            'a.date' => $date,
        );
        $queue = $this->Queue_display->select(0, $select_queue, $where_queue);

        /* Start Transaction */
        $this->db->trans_start();

        $updated_status = 0;
        for ($i=0; $i < count($queue); $i++) { 
            $queue_data = explode(',', $queue[$i]->data);
            $queue_status = $queue[$i]->status;

            if (in_array($queue_number, $queue_data) == 1) {
                $idx_remove = array_search($queue_number, $queue_data);
                unset($queue_data[$idx_remove]);

                $updated_status = $queue_status - 1;

                if ($updated_status < 1) {
                    $this->REST_Return(409, 'queue number ' . $queue_number . ' is in the first step');
                    return;
                }

                $dt_queue = array(
                    'name' => 'PRJ',
                    'data' => implode(',', $queue_data),
                    'date' => $date,
                    'status' => $queue_status,
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Queue_display->replace($dt_queue);
            }
        }

        if ($updated_status == 0) {
            $this->REST_Return(404, 'there is no queue number ' . $queue_number . ' in queue');
            return;
        }

        for ($i=0; $i < count($queue); $i++) { 
            $queue_data = ($queue[$i]->data == '') ? [] : explode(',', $queue[$i]->data);
            $queue_status = $queue[$i]->status;

            if ($updated_status == $queue_status) {
                array_push($queue_data, $queue_number);

                $dt_queue = array(
                    'name' => 'PRJ',
                    'data' => implode(',', $queue_data),
                    'date' => $date,
                    'status' => $queue_status,
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Queue_display->replace($dt_queue);
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to queue next', $response_data);
            return;
        }
    
        $response_data = array(
            'queue_number'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    /**
     * GET Prescription with API
     * -------------------------
     * @method: GET
     */

    //  public function detail_concoct_get($p = 0) {
    //     header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = $this->name;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] , $response_data);
    //         return;
    //     } 

    //     $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    //     if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
    //     if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
        
    //     $registration_id = '';
    //     if(isset($q_strings['registration_id'])){
    //         $registration_id = $q_strings['registration_id']; 
    //         unset($q_strings['registration_id']);
    //     }
    //     $registration_destination_id = '';
    //     if(isset($q_strings['registration_destination_id'])){
    //         $registration_destination_id = $q_strings['registration_destination_id']; 
    //         unset($q_strings['registration_destination_id']);
    //     }
    //     if(isset($q_strings['norm'])){
    //         $norm = $q_strings['norm']; 
    //         unset($q_strings['norm']);
    //     }
    //     $this->$model->whereFields = $q_strings;
    //     if(isset($q_strings['doctor_in_charge_id'])) $this->$model->whereFields['a.doctor_in_charge_id'] = $q_strings['doctor_in_charge_id']; unset($this->$model->whereFields['doctor_in_charge_id']);
    //     if($registration_id != ''){
    //         $this->$model->whereFields['rd.registration_id'] = $registration_id; 
    //     };
    //     if($registration_destination_id != ''){
    //         $this->$model->whereFields['a.registration_destination_id'] = $registration_destination_id; 
    //     };
    //     if($norm != ''){
    //         $this->$model->whereFields['a.norm'] = $norm; 
    //     };
    //     $this->$model->whereFields['a.is_delete'] = 0;
    //     $output = $this->$model->read($id);
    //     $response_data = array();
    //     if ($output) {
    //         for ($i=0; $i < count($output); $i++) { 
    //             $this->Prescription_item->whereFields['a.is_delete'] = 0;
    //             $form_prescription = $this->Prescription_item->read($output[$i]->id);
    //             if ($form_prescription != []) {
    //                 $done2 = 0;
    //                 $done3 = 0;
    //                 $done4 = 0;
    //                 $done5 = 0;
    //                 $pres_blank = array();
    //                 for ($j=0; $j < count($form_prescription); $j++) { 
    //                     $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
    //                     $resep = array(
    //                         'pharmacy_prescription_id' => $form_prescription[$j]->pharmacy_prescription_id,
    //                         'pharmacy_catalog_id' => $form_prescription[$j]->pharmacy_catalog_id,
    //                         'pharmacy_catalog_name' => $catalog->name,
    //                         'pharmacy_catalog_code' => $catalog->code,
    //                         'prescription_item_type_id' => $form_prescription[$j]->prescription_item_type_id,
    //                         'prescription_item_type_used' => $form_prescription[$j]->prescription_item_type_used,
    //                         'prescription_item_type_value' => $form_prescription[$j]->prescription_item_type_value,
    //                         'package_id' => $form_prescription[$j]->package_id,
    //                         'package_name' => $form_prescription[$j]->package_name,
    //                         'dose' => $form_prescription[$j]->dose,
    //                         'dose_name' => $catalog->dose_name,
    //                         'dose_doctor' => $form_prescription[$j]->dose_doctor,
    //                         'batch' => $form_prescription[$j]->batch,
    //                         'expired' => $form_prescription[$j]->expired,
    //                         'qty_doctor' => $form_prescription[$j]->qty_doctor,
    //                         'qty' => $form_prescription[$j]->qty,
    //                         'qty_retur' => $form_prescription[$j]->qty_retur,
    //                         'qty_copy' => $form_prescription[$j]->qty_copy,
    //                         'is_retur' => $form_prescription[$j]->is_retur,
    //                         'is_retur_full' => $form_prescription[$j]->is_retur_full,
    //                         'used' => $form_prescription[$j]->used,
    //                         'desc' => $form_prescription[$j]->desc,
    //                         'var_1' => $form_prescription[$j]->price*1.3,
    //                         'var_2' => $form_prescription[$j]->qty*($form_prescription[$j]->price*1.3),
    //                         'var_3' => $form_prescription[$j]->var_3,
    //                         'var_4' => $form_prescription[$j]->var_4,
    //                         'doctor_in_charge_id' => $form_prescription[$j]->doctor_in_charge_id,
    //                         'pharmacy_catalog_id_ref' => $form_prescription[$j]->pharmacy_catalog_id_ref,
    //                         'pharmacy_catalog_id_retur' => $form_prescription[$j]->pharmacy_catalog_id_retur,
    //                         'pharmacy_catalog_id_sub' => $form_prescription[$j]->pharmacy_catalog_id_sub,
    //                         'sort' => $form_prescription[$j]->sort,
    //                     );
    //                     if ($form_prescription[$j]->prescription_item_type_id == 1) {
    //                         array_push($pres_blank, $resep);
    //                     } else if ($form_prescription[$j]->prescription_item_type_id == 2) {
    //                         if ($done2 == 1) {
    //                             continue;
    //                         }
    //                         array_push($pres_blank, $resep);
    //                         $done2 = 1;
    //                     } else if ($form_prescription[$j]->prescription_item_type_id == 3) {
    //                         if ($done3 == 1) {  
    //                             continue;
    //                         }
    //                         array_push($pres_blank, $resep);
    //                         $done3 = 1;
    //                     } else if ($form_prescription[$j]->prescription_item_type_id == 4) {
    //                         if ($done4 == 1) {
    //                             continue;
    //                         }
    //                         array_push($pres_blank, $resep);
    //                         $done4 = 1;
    //                     } else if ($form_prescription[$j]->prescription_item_type_id == 5) {
    //                         if ($done5 == 1) {
    //                             continue;
    //                         }
    //                         array_push($pres_blank, $resep);
    //                         $done5 = 1;
    //                     }
    //                     $output[$i]->form_prescription = $pres_blank;
    //                         // $output[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
    //                         // $output[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
    //                         // $output[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $catalog->name;
    //                         // $output[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $catalog->code;
    //                         // $output[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
    //                         // $output[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
    //                         // $output[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
    //                         // $output[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
    //                         // $output[$i]->form_prescription[$j]['dose_name'] = $catalog->dose_name;
    //                         // $output[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
    //                         // $output[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
    //                         // $output[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
    //                         // $output[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
    //                         // $output[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
    //                         // $output[$i]->form_prescription[$j]['qty_retur'] = $form_prescription[$j]->qty_retur;
    //                         // $output[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
    //                         // $output[$i]->form_prescription[$j]['is_retur'] = $form_prescription[$j]->is_retur;
    //                         // $output[$i]->form_prescription[$j]['is_retur_full'] = $form_prescription[$j]->is_retur_full;
    //                         // $output[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
    //                         // $output[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
    //                         // $output[$i]->form_prescription[$j]['var_1'] = $form_prescription[$j]->price*1.3;
    //                         // $output[$i]->form_prescription[$j]['var_2'] = $form_prescription[$j]->qty*($form_prescription[$j]->price*1.3);
    //                         // $output[$i]->form_prescription[$j]['var_3'] = $form_prescription[$j]->var_3;
    //                         // $output[$i]->form_prescription[$j]['var_4'] = $form_prescription[$j]->var_4;
    //                         // $output[$i]->form_prescription[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
    //                         // $output[$i]->form_prescription[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
    //                         // $output[$i]->form_prescription[$j]['pharmacy_catalog_id_retur'] = $form_prescription[$j]->pharmacy_catalog_id_retur;
    //                         // $output[$i]->form_prescription[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
    //                         // $output[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
    //                         // $done = 1;
    //                 }
    //             } 
    //         }
    //     }
    //     if ($output != [] && $id != 0) {
    //         $response_data = $output;
    //         $error   = false;
    //     } else if($output != [] or $id == 0) {
    //         $response_data = $output;
    //         $error   = false;
    //     } else {
    //         $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
    //         return;
    //     }
    //     if ($error == true) {
    //         $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //     } else {
    //         $this->REST_Return(200, 'SUCCESS', $response_data);
    //     }
    //     return;
    // }

    // public function list_get($p = 0){
	// 	header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = $this->name;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    //         if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
    //         if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
    //         // $this->$model->whereLike = $q_strings;
    //         $this->$model->whereFields['hf.institute_branch_id'] = $institute_branch_id;
    //         $this->$model->whereFields['hri.institute_branch_id'] = $institute_branch_id;
    //         if ($q_strings != []){
    //             $output = $this->$model->filter_data($q_strings,true);
    //         } else {
    //             $output = $this->$model->read_ssr($id);
    //         }
    //         $response_data = array();
    //         for ($i=0; $i < count($output->rows); $i++) {
    //             $form_prescription = $this->Prescription_item->read($output->rows[$i]->id);
    //             if ($form_prescription != []) {
    //                 for ($j=0; $j < count($form_prescription); $j++) { 
    //                     $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
    //                     $output->rows[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
    //                     $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
    //                     $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $catalog->name;
    //                     $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $catalog->code;
    //                     $output->rows[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
    //                     $output->rows[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
    //                     $output->rows[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
    //                     $output->rows[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
    //                     $output->rows[$i]->form_prescription[$j]['dose_name'] = $catalog->dose_name;
    //                     $output->rows[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
    //                     $output->rows[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
    //                     $output->rows[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
    //                     $output->rows[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
    //                     $output->rows[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
    //                     $output->rows[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
    //                     $output->rows[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
    //                     $output->rows[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
    //                     $output->rows[$i]->form_prescription[$j]['var_1'] = $form_prescription[$j]->var_1;
    //                     $output->rows[$i]->form_prescription[$j]['var_2'] = $form_prescription[$j]->var_2;
    //                     $output->rows[$i]->form_prescription[$j]['var_3'] = $form_prescription[$j]->var_3;
    //                     $output->rows[$i]->form_prescription[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
    //                     $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
    //                     $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
    //                     $output->rows[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
    //                 }
    //             } 
    //         }
    //         if ($output->rows != [] && $id != 0) {
    //             $response_data = $output->rows[0];
    //             $error   = false;
    //         } else if($output->rows != [] or $id == 0) {
    //             $response_data = $output;
    //             $error   = false;
    //         } else {
    //             $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
    //             return;
    //         }
    //         if ($error == true) {
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             $this->REST_Return(200, 'SUCCESS', $response_data);
    //         }
    //         return;
    //     } else {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
	// }


    /**
     * Prescription API
     * --------------------
     * @param: pharmacy_depo_id
     * @param: soap_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: flag
     * @param: date_added
     * --------------------
     * @method : POST
     * @link: version/mr/prescription
     **/

    public function update_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->update_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        // 4C-03C-0: branch & actor hanya dari token, tidak pernah dari payload.
        // Kosong => fail-closed sebelum transaksi dimulai (tidak ada tulisan apa pun).
        $token_branch   = trim((string) $this->security->xss_clean($is_valid_token['data']->institute_branch_id));
        $token_username = isset($is_valid_token['data']->username) ? $is_valid_token['data']->username : null;
        if ($token_branch === '') {
            $this->_c30_audit('DENIED_BRANCH_SCOPE_UNDEFINED', $id, $user_id, $token_username,
                array('reason' => 'branch_empty'), 409);
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return;
        }
    
        $prescription_id = $id;
    
        if ($prescription_id === 0 || $prescription_id === "0") {
            $this->REST_Return(400, 'prescription_id is required');
            return;
        }

        $select = 'a.id';
        $prescription = $this->$model->select($prescription_id, $select);

        if (count($prescription) == 0) {
            $this->REST_Return(404, 'there is no prescription with id ' . $prescription_id);
            return;
        }

        $form_prescription = $dt_put['form_prescription'];
        $form_prescription_parent = $dt_put['form_prescription_parent'];
    
        /* Start Transaction */
        $this->db->trans_start();

        // 4C-03C-0: locking read + immutability guard sebagai validasi AWAL.
        // Guard dijalankan sebelum soft-delete/insert ulang apa pun, sehingga penolakan
        // tidak pernah menghasilkan UPDATE, soft-delete, atau insert ulang.
        $rx_lock_q = $this->db->query(
            "SELECT id, flag, is_delete, institute_branch_id, pharmacy_depo_id, registration_destination_id FROM pharmacy_prescription WHERE id = ? FOR UPDATE",
            array($prescription_id)
        );
        $rx_lock = ($rx_lock_q === FALSE) ? null : $rx_lock_q->row();
        if ($rx_lock === null) {
            $this->db->trans_rollback();
            $this->REST_Return(404, 'there is no prescription with id ' . $prescription_id);
            return;
        }

        // Branch scope: nilai resep harus sama dengan branch pada token.
        if ((string) $rx_lock->institute_branch_id !== $token_branch) {
            $this->db->trans_rollback();
            $this->_c30_audit('DENIED_BRANCH_SCOPE_MISMATCH', $prescription_id, $user_id, $token_username, array(
                'reason'       => 'branch_mismatch',
                'rx_branch'    => $rx_lock->institute_branch_id,
                'token_branch' => $token_branch,
            ), 403);
            $this->REST_Return(403, 'Resep berada di luar branch scope.', array(), 'BRANCH_SCOPE_MISMATCH');
            return;
        }

        // Immutability: resep yang sudah diserahkan tidak boleh diubah, di-soft-delete,
        // atau dihapus lalu dibuat ulang.
        if ((int) $rx_lock->flag === 1) {
            $this->db->trans_rollback();
            $this->_c30_audit('DENIED_ALREADY_DISPENSED', $prescription_id, $user_id, $token_username, array(
                'reason' => 'flag_already_1',
                'flag'   => (int) $rx_lock->flag,
            ), 409);
            $this->REST_Return(409, 'Resep sudah diserahkan dan tidak dapat diubah.', array(
                'prescription_id' => $prescription_id,
                'flag'            => 1,
            ), 'PRESCRIPTION_ALREADY_DISPENSED');
            return;
        }

        if ($prescription_id != '' && $prescription_id != null) {
            $dt_prescription_item = array(
                'is_delete'                 => 1,
            );
            $this->Prescription_item->update($prescription_id, $dt_prescription_item);
        }

        // 4C-03C: identitas penjamin dari DB (registration_insurance), depo dari baris resep.
        $c3c_identity  = $this->_c3c_identity($rx_lock->registration_destination_id);
        $c3c_date      = date('Y-m-d');
        $c3c_depo      = (string) $rx_lock->pharmacy_depo_id;
        $c3c_snapshots = array();

        for ($i=0; $i < count($form_prescription); $i++) { 
            if ($form_prescription[$i]['pharmacy_catalog_id'] == null) {
                $this->REST_Return(400, 'pharmacy_catalog_id in form_prescription is required');
                return;
            }

            if ($form_prescription[$i]['package_id'] == null) {
                $this->REST_Return(400, 'package_id in form_prescription is required');
                return;
            }

            $pharmacy_catalog_id = $form_prescription[$i]['pharmacy_catalog_id'];
            $package_id = $form_prescription[$i]['package_id'];
            $signatura_times_id = $form_prescription[$i]['signatura_times_id'] ?? null;
            $administer_id = $form_prescription[$i]['administer_id'] ?? null;
            $dose_unit_id = $form_prescription[$i]['dose_unit_id'] ?? null;

            $select = 'a.id';
            $pharmacy_catalog = $this->Catalog->select($pharmacy_catalog_id, $select);

            if (count($pharmacy_catalog) == 0) {
                $this->REST_Return(404, 'there is no pharmacy_catalog with id ' . $pharmacy_catalog_id);
                return;
            }

            $select = 'a.id, a.name';
            $package = $this->Package->select($package_id, $select);

            if (count($package) == 0) {
                $this->REST_Return(404, 'there is no package with id ' . $package_id);
                return;
            }

            $package_name = $package[0]->name;

            if ($signatura_times_id != null) {
                $select = 'a.id';
                $signatura_times = $this->Signatura_times->select($signatura_times_id, $select);
                if (count($signatura_times) == 0) {
                    $this->REST_Return(404, 'signatura times with id ' . $signatura_times_id . ' does not exist');
                    return;
                }
            }

            if ($administer_id != null) {
                $select = 'a.id';
                $administer = $this->Administer->select($administer_id, $select);
                if (count($administer) == 0) {
                    $this->REST_Return(404, 'administer with id ' . $administer_id . ' does not exist');
                    return;
                }
            }

            if ($dose_unit_id != null) {
                $select = 'a.id';
                $dose_unit = $this->Unit->select($dose_unit_id, $select);
                if (count($dose_unit) == 0) {
                    $this->REST_Return(404, 'unit with id ' . $dose_unit_id . ' does not exist');
                    return;
                }
            }

            $prescription_item_type_id = $form_prescription[$i]['prescription_type_id'];
            $dose = $form_prescription[$i]['dose'];
            $dose_doctor = $form_prescription[$i]['sum_dose'];
            $qty_doctor = $form_prescription[$i]['value_package'];
            $qty = $form_prescription[$i]['value_package'];
            $qty_copy = $form_prescription[$i]['qty_copy'];
            $sort = $form_prescription[$i]['sort'];
            $used = $form_prescription[$i]['used'];
            $desc = $form_prescription[$i]['desc'];
            $status_kardex = clean_postput($form_prescription[$i]['status_kardex'] ?? null);

            for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                $parent_id = $form_prescription_parent[$ip]['id'];
                $parent_used = $form_prescription_parent[$ip]['used'] ?? null;
                $parent_value = $form_prescription_parent[$ip]['value'] ?? null;
                $parent_package = $form_prescription_parent[$ip]['package'] ?? null;
                $parent_signatura_times_id = $form_prescription_parent[$ip]['signatura_times_id'] ?? null;
                $parent_administer_id = $form_prescription_parent[$ip]['administer_id'] ?? null;
                $parent_desc = clean_postput($form_prescription_parent[$ip]['desc'] ?? null);
                $parent_status_kardex = clean_postput($form_prescription_parent[$ip]['status_kardex'] ?? null);

                if ($parent_id == $prescription_item_type_id) {
                    if ($parent_signatura_times_id != null) {
                        $select = 'a.id';
                        $signatura_times = $this->Signatura_times->select($parent_signatura_times_id, $select);
                        if (count($signatura_times) == 0) {
                            $this->REST_Return(404, 'signatura times with id ' . $parent_signatura_times_id . ' does not exist');
                            return;
                        }
                        $signatura_times_id = $parent_signatura_times_id;
                    }

                    if ($parent_administer_id != null) {
                        $select = 'a.id';
                        $administer = $this->Administer->select($parent_administer_id, $select);
                        if (count($administer) == 0) {
                            $this->REST_Return(404, 'administer with id ' . $parent_administer_id . ' does not exist');
                            return;
                        }
                        $administer_id = $parent_administer_id;
                    }

                    if ($parent_desc != null) {
                        $desc = $parent_desc;
                    }

                    if ($parent_status_kardex != null) {
                        $status_kardex = $parent_status_kardex;
                    }

                    $dt_prescription_item = array(
                        'pharmacy_prescription_id'      => $prescription_id,
                        'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                        'prescription_item_type_id'     => $prescription_item_type_id,
                        'prescription_item_type_used'   => $parent_used,
                        'prescription_item_type_value'  => $parent_value,
                        'prescription_item_type_package'=> $parent_package,
                        'package_id'                    => $package_id,
                        'package_name'                  => $package_name,
                        'signatura_times_id'            => $signatura_times_id,
                        'administer_id'                 => $administer_id,
                        'dose'                          => $dose,
                        'dose_unit_id'                  => $dose_unit_id,
                        'dose_doctor'                   => $dose_doctor,
                        'qty_doctor'                    => $qty_doctor,
                        'qty'                           => $qty,
                        'qty_copy'                      => $qty_copy,
                        'sort'                          => $sort,
                        'used'                          => $used,
                        'desc'                          => $desc,
                        'status_kardex'                 => $status_kardex,
                        'is_delete'                     => 0,
                    );

                    // 4C-03C: snapshot harga dari server; var_1 legacy = price_sale server-side.
                    $c3c = $this->_c3c_resolve_price($pharmacy_catalog_id, $token_branch, $c3c_depo,
                        $c3c_identity['service_class_id'], $c3c_identity['insurance_id'], $c3c_date);
                    if ($c3c['ok'] !== true) {
                        $this->db->trans_rollback();
                        $this->_c30_audit('DENIED_' . $c3c['code'], $prescription_id, $user_id, $token_username, array(
                            'reason'       => $c3c['code'],
                            'catalog_id'   => $pharmacy_catalog_id,
                            'depo_id'      => $c3c_depo,
                            'branch_id'    => $token_branch,
                            'class_id'     => $c3c_identity['service_class_id'],
                            'insurance_id' => $c3c_identity['insurance_id'],
                            'price_date'   => $c3c_date,
                        ), 409, 'update');
                        $this->REST_Return(409, ($c3c['code'] === 'PRICE_AMBIGUOUS'
                                ? 'Harga katalog ambigu; tidak ada satu harga canonical.'
                                : 'Harga katalog tidak ditemukan untuk dimensi resep ini.'),
                            array('reason' => $c3c['code'], 'pharmacy_catalog_id' => $pharmacy_catalog_id),
                            $c3c['code']);
                        return;
                    }
                    $dt_prescription_item = array_merge($dt_prescription_item, $this->_c3c_snapshot($c3c['row']));
                    $c3c_snapshots[] = array(
                        'pharmacy_catalog_id' => $pharmacy_catalog_id,
                        'price_id'            => $c3c['row']->id,
                        'price_sale'          => $c3c['row']->price_sale,
                    );

                    $this->Prescription_item->replace($dt_prescription_item);
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update prescription', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );

        // 4C-03C: audit harga server yang dipakai (bukan harga client).
        $this->_c30_audit('PRICE_SNAPSHOT_OK', $id, $user_id, $token_username, array(
            'branch_id'    => $token_branch,
            'depo_id'      => $c3c_depo,
            'class_id'     => $c3c_identity['service_class_id'],
            'insurance_id' => $c3c_identity['insurance_id'],
            'items'        => $c3c_snapshots,
        ), 200, 'update');

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function transaction_put($p = 0) {
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
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            // 4C-02: branch hanya dari token/authorization context, tidak pernah dari payload.
            // Kosong => fail-closed (409), tidak ada tulisan apa pun.
            $branch = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if (trim((string) $branch) === '') {
                $this->_c2_audit('DENIED_BRANCH_SCOPE_UNDEFINED', $id, $user_id, array('reason' => 'branch_empty'), 409);
                $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
                return;
            }
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('flag', 'flag', 'trim|max_length[1]');
            $this->form_validation->set_rules('form_prescription[]', 'form_prescription', 'trim|required|max_length[8]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                
                /* Start Transaction */
                $this->db->trans_start();

                // 4C-02 R2: kunci baris resep (sumber idempotensi) sebelum apa pun dibaca/ditulis.
                // Locking read selalu melihat versi commit terbaru, jadi dua request paralel
                // untuk resep yang sama diserialkan di sini: yang kedua melihat flag = 1.
                $rx_lock_q = $this->db->query(
                    "SELECT id, flag, is_delete, institute_branch_id FROM pharmacy_prescription WHERE id = ? FOR UPDATE",
                    array($id)
                );
                $rx_lock = ($rx_lock_q === FALSE) ? null : $rx_lock_q->row();
                if ($rx_lock === null) {
                    $this->db->trans_rollback();
                    $this->REST_Return(404, 'Prescription with id ' . $id . ' does not exist!');
                    return;
                }
                if ((int) $rx_lock->flag === 1) {
                    $this->db->trans_rollback();
                    $this->_c2_audit('DENIED_ALREADY_DISPENSED', $id, $user_id, array('reason' => 'flag_already_1'), 409);
                    $this->REST_Return(409, 'Resep sudah diserahkan.', array(
                        'prescription_id' => $id,
                        'flag'            => 1,
                    ), 'PRESCRIPTION_ALREADY_DISPENSED');
                    return;
                }
                // Branch scope: nilai resep harus sama dengan branch pada token. NULL/berbeda => tolak.
                if ((string) $rx_lock->institute_branch_id !== (string) $branch) {
                    $this->db->trans_rollback();
                    $this->_c2_audit('DENIED_BRANCH_SCOPE_MISMATCH', $id, $user_id, array(
                        'reason'         => 'branch_mismatch',
                        'rx_branch'      => $rx_lock->institute_branch_id,
                        'token_branch'   => $branch,
                    ), 403);
                    $this->REST_Return(403, 'Resep berada di luar branch scope.', array(), 'BRANCH_SCOPE_MISMATCH');
                    return;
                }
                
                $prescription = $this->$model->read($id);
                if(count($prescription) == 0) {
                    $this->db->trans_rollback();
                    $this->REST_Return(404, 'Prescription with id ' . $id . ' does not exist!');
                    return;
                }

                $registration_destination_id = $prescription[0]->registration_destination_id;

                $depo = $this->Depo->read($dt_put['pharmacy_depo_id'], false);
                if(count($depo) == 0) {
                    $this->REST_Return(404, 'Wrong pharmacy_depo_id or soap_id!');
                    return;
                }

                // 4C-02 R1: baca item + validasi stok SELURUH item (dan kunci barisnya) sebelum
                // ada penulisan apa pun. Satu item gagal => rollback total, tidak ada item tertulis.
                $tmp_concoction = [];
                $price_changes  = [];
                $this->Prescription_item->whereFields['a.is_delete'] = 0;
                $prescription_item = $this->Prescription_item->read($id,false);

                // 4C-03D: harga penyerahan WAJIB dari snapshot server-side pada baris resep.
                // Payload client (var_1/hja/hna/var_3) tidak pernah dibaca sebagai harga.
                // Snapshot tidak ada => tolak sebelum ada tulisan apa pun (fail-closed).
                $price_error = $this->_c3d_snapshot_guard($prescription_item);
                if ($price_error !== null) {
                    $this->db->trans_rollback();
                    $this->_c2_audit('DENIED_PRICE_SNAPSHOT_MISSING', $id, $user_id, array(
                        'reason'              => $price_error['reason'],
                        'index'               => $price_error['index'],
                        'pharmacy_catalog_id' => $price_error['pharmacy_catalog_id'],
                        'price_id'            => $price_error['price_id'],
                        'old_price'           => $price_error['old_price'],
                        'new_price'           => null,
                        'snapshot_date'       => null,
                        'applied_by'          => $user_id,
                        'applied_from_branch' => $branch,
                    ), 409);
                    $this->REST_Return(409, 'Harga obat pada resep belum memiliki snapshot server.',
                        $price_error, 'PRICE_SNAPSHOT_MISSING');
                    return;
                }

                $stock_error = $this->_c2_check_stock($dt_put, $prescription_item, $dt_put['pharmacy_depo_id']);
                if ($stock_error !== null) {
                    $this->db->trans_rollback();
                    $http = ($stock_error['reason'] === 'STOCK_INSUFFICIENT') ? 409 : 422;
                    $this->_c2_audit('DENIED_' . $stock_error['reason'], $id, $user_id, $stock_error, $http);
                    $this->REST_Return($http,
                        ($http === 409) ? 'Stok tidak mencukupi.' : 'Jumlah obat tidak valid.',
                        $stock_error, $stock_error['reason']);
                    return;
                }

                $dt_prescription = array(
                    'id'                        => $id,
                    'pharmacy_depo_id'          => $dt_put['pharmacy_depo_id'],
                    'health_facility_item_id'   => isset($dt_put['health_facility_item_id']) ? $dt_put['health_facility_item_id'] : $prescription[0]->health_facility_item_id,
                    // P0-4: nilai tagihan header tidak diambil dari payload klien; dihitung ulang
                    // dari item setelah seluruh baris ditulis (lihat _bill_total).
                    'var_1'                     => $prescription[0]->var_1,
                    'var_2'                     => $prescription[0]->var_2,
                    'var_3'                     => $prescription[0]->var_3,
                    'flag'                      => 1,
                    'user_1'                    => $user_id,
                    'date_1'                    => date("Y-m-d H:i:s",time()),
                );

                // echo "<pre>";
                // var_dump($dt_prescription);
                // echo "<pre>";
                // die();
                
                $this->$model->replace($dt_prescription);

                // 4C-02: seluruh penulisan item dibungkus supaya error tak terduga di tengah loop
                // (mis. katalog hilang dari master) tetap berakhir rollback + audit, bukan
                // separuh item tersimpan tanpa jejak. Badan loop sengaja tidak di-reindent.
                try {
                for ($i=0; $i < count($prescription_item); $i++) { 
                    $pharmacy_catalog_id = $prescription_item[$i]->pharmacy_catalog_id;
                    $pharmacy_catalog_name = $prescription_item[$i]->pharmacy_catalog_name;

                    $select = 'a.id, a.registration_destination_id, ki.dose, ki.dose_unit_id, ki.signatura_times_id, ki.administer_id, pc.dose as catalog_dose, pc.dose_unit_id as catalog_dose_unit_id, pc.packaging_value as catalog_packaging_value, pc.packaging_unit_id as catalog_packaging_unit_id, ki.prescription_item_type_id, ki.prescription_item_type_value';
                    $where = array(
                        'a.pharmacy_prescription_id' => $id,
                        'ki.pharmacy_catalog_id' => $pharmacy_catalog_id,
                    );
                    $join = array(
                        array('kardex_item ki', 'a.id = ki.kardex_id', 'INNER'),
                        array('pharmacy_catalog pc', 'ki.pharmacy_catalog_id = pc.id', 'INNER'),
                    );
                    $kardex = $this->Kardex->select(0, $select, $where, $join);

                    if (count($kardex) > 0) {
                        $pharmacy_qty = (float) $dt_put['form_prescription'][$i]['qty'];

                        $kdx_pharmacy_catalog_id = $pharmacy_catalog_id;
                        $kdx_registration_destination_id = $kardex[0]->registration_destination_id;
                        $kdx_dose = $kardex[0]->dose;
                        $kdx_dose_unit_id = $kardex[0]->dose_unit_id;
                        $kdx_signatura_times_id = $kardex[0]->signatura_times_id;
                        $kdx_administer_id = $kardex[0]->administer_id;
                        $kdx_catalog_dose = $kardex[0]->catalog_dose;
                        $kdx_catalog_dose_unit_id = $kardex[0]->catalog_dose_unit_id;
                        $kdx_catalog_packaging_value = $kardex[0]->catalog_packaging_value;
                        $kdx_catalog_packaging_unit_id = $kardex[0]->catalog_packaging_unit_id;
                        $kdx_prescription_item_type_id = $kardex[0]->prescription_item_type_id;

                        $form_prescription_parent = $dt_put['form_prescription_parent'];

                        // Define data item parent
                        for ($j=0; $j < count($form_prescription_parent); $j++) { 
                            $id_parent = $form_prescription_parent[$j]['id'];
                            if ($id_parent == 1) {
                                continue;
                            }
                            if ($id_parent == $kdx_prescription_item_type_id) {
                                $parent_prescription_type_package = $form_prescription_parent[$j]['package'] ?? null;
                                $parent_prescription_type_value = $form_prescription_parent[$j]['value'] ?? null;
                                $parent_prescription_type_pharmacy_catalog_id = $form_prescription_parent[$j]['pharmacy_catalog_id'] ?? null;  
                                $parent_prescription_type_qty = $form_prescription_parent[$j]['qty'] ?? null;  

                                if ($parent_prescription_type_pharmacy_catalog_id == null) {
                                    $this->REST_Return(400, 'pharmacy_catalog_id in parent is required');
                                    return;
                                }

                                if ($parent_prescription_type_qty == null) {
                                    $this->REST_Return(400, 'qty in parent is required');
                                    return;
                                }

                                $select = 'a.id, a.package_id, c.name as package_name';
                                $parent_catalog = $this->Catalog->select($parent_prescription_type_pharmacy_catalog_id, $select);
                                if (count($parent_catalog) == 0) {
                                    $this->REST_Return(404, 'there is no parent_catalog with id ' . $parent_prescription_type_pharmacy_catalog_id);
                                    return;
                                }

                                $parent_package_id = $parent_catalog[0]->package_id;
                                $parent_package_name = $parent_catalog[0]->package_name;
                            }
                        }
                        
                        $kdx_dose = clean_dose_unit($kdx_dose, $kdx_dose_unit_id, $pharmacy_catalog_id);
                        $kdx_dose_unit_id = clean_unit($kdx_dose_unit_id);
                        $kdx_dose_real = $kdx_dose;
                        $kdx_dose_unit_id_real = $kdx_dose_unit_id;

                        Logger::debug($this->debug_uniq, "--catalog: $pharmacy_catalog_name | dose_doctor: $kdx_dose $kdx_dose_unit_id | prescription_type: $kdx_prescription_item_type_id");
                        // Logger::debug($this->debug_uniq, "--catalog: $pharmacy_catalog_name | $catalog_dose$catalog_dose_unit_name | $catalog_packaging_value$catalog_packaging_unit_name | status $status | $package_name");
                        
                        if ($kdx_prescription_item_type_id != 1 && !in_array($kdx_prescription_item_type_id, $tmp_concoction)) {
                            $pharmacy_qty = $parent_prescription_type_qty;
                            $kdx_pharmacy_catalog_id = $parent_prescription_type_pharmacy_catalog_id;
                            $kdx_dose = $parent_prescription_type_value;
                            $kdx_catalog_packaging_value = 0;

                            if ($parent_prescription_type_package == 1 || $parent_prescription_type_package == 2 || $parent_prescription_type_package == 3 || $parent_prescription_type_package == 5) {
                                $kdx_catalog_packaging_unit_id = '61a257e9-2e4e-4e47-8387-0bc0fe90aeec';
                            } elseif ($parent_prescription_type_package == 4) {
                                $kdx_catalog_packaging_unit_id = 'ab328f03-5088-49e3-9f0d-c3ea95d0747d';
                            }

                            if ($parent_prescription_type_package == 1) {
                                $kdx_dose_unit_id = '32df1bea-5de0-41fc-b869-68820c221599'; // PUYER
                            } elseif ($parent_prescription_type_package == 2) {
                                $kdx_dose_unit_id = 'b0ae0afd-acc4-4654-86ce-e9417f28253a'; // KAPSUL
                            } elseif ($parent_prescription_type_package == 3) {
                                $kdx_dose_unit_id = '1787de8f-9974-4717-ba15-f6f3a2a8e8fe'; // TUBE
                            } elseif ($parent_prescription_type_package == 5) {
                                $kdx_dose_unit_id = '1787de8f-9974-4717-ba15-f6f3a2a8e8fe'; // TUBE
                            }

                            // parent_package: [
                            //     { value: 1, title: 'PUYER' },
                            //     { value: 2, title: 'KAPSUL' },
                            //     { value: 3, title: 'SALEP' },
                            //     { value: 4, title: 'SIRUP' },
                            //     { value: 5, title: 'POT' },
                            // ],

                            $dt_prescription_item = array(
                                'pharmacy_prescription_id'      => $id,
                                'pharmacy_catalog_id'           => $parent_prescription_type_pharmacy_catalog_id,
                                'prescription_item_type_id'     => $kdx_prescription_item_type_id,
                                'package_id'                    => $parent_package_id,
                                'package_name'                  => $parent_package_name,
                                'dose'                          => $parent_prescription_type_value,
                                'qty'                           => $parent_prescription_type_qty,
                                // 'sort'                          => $dt_put['form_prescription'][$i]['sort'],
                            );
                            $this->Prescription_item->replace($dt_prescription_item);
                        } 

                        $select = 'a.sort';
                        $where = array(
                            'a.registration_destination_id' => $kdx_registration_destination_id,
                        );
                        $join = array();
                        $order = array(
                            'a.sort' => 'DESC',
                        );
                        $kardex_stock = $this->Kardex_stock->select(0, $select, $where, $join, $order);
                        $sort_stock = 0;
                        if (count($kardex_stock) > 0) {
                            $sort_stock = $kardex_stock[0]->sort + 1;
                        }
                        
                        if ($kdx_prescription_item_type_id == 1 || ($kdx_prescription_item_type_id != 1 && !in_array($kdx_prescription_item_type_id, $tmp_concoction))) {
                            $select = 'a.id, a.current_amount, a.current_amount_unit_id, a.current_physical_quantity';
                            $where = array(
                                'a.registration_destination_id' => $kdx_registration_destination_id,
                                'a.pharmacy_catalog_id' => $kdx_pharmacy_catalog_id,
                            );
                            $kardex_stock = $this->Kardex_stock->select(0, $select, $where);
                            if (count($kardex_stock) == 0) {
                                $dt_kardex_stock = array(
                                    'registration_destination_id' => $kdx_registration_destination_id,
                                    'pharmacy_catalog_id' => $kdx_pharmacy_catalog_id,
                                    'signatura_times_id' => $kdx_signatura_times_id,
                                    'dose' => $kdx_dose,
                                    'dose_unit_id' => $kdx_dose_unit_id,
                                    'current_amount' => 0,
                                    'current_amount_unit_id' => $kdx_catalog_packaging_unit_id,
                                    'current_physical_quantity' => 0,
                                    'date_added' => date('Y-m-d H:i:s'),
                                    'date_updated' => date('Y-m-d H:i:s'),
                                    'sort' => $sort_stock,
                                );
                        
                                $kdx_stock_id = $this->Kardex_stock->create($dt_kardex_stock);
    
                                $kdx_stock_current_amount = 0;
                                $kdx_stock_current_amount_unit_id = $kdx_catalog_packaging_unit_id;
                                $kdx_stock_current_physical_quantity = 0;

                                if ($kdx_prescription_item_type_id != 1) {
                                    $tmp_concoction[] = $kdx_prescription_item_type_id;

                                    $select = 'a.id';
                                    $where = array(
                                        'a.registration_destination_id' => $kdx_registration_destination_id,
                                        'a.parent_pharmacy_catalog_id' => $parent_prescription_type_pharmacy_catalog_id,
                                        'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
                                    );
                                    $kdx_stock_concoction = $this->Kardex_stock_concoction->select(0, $select, $where);
                                    if (count($kdx_stock_concoction) == 0) {
                                        $dt_kardex_stock_concoction = array(
                                            'registration_destination_id' => $kdx_registration_destination_id,
                                            'parent_pharmacy_catalog_id' => $parent_prescription_type_pharmacy_catalog_id,
                                            'pharmacy_catalog_id' => $pharmacy_catalog_id,
                                            'dose' => $kdx_dose_real,
                                            'dose_unit_id' => $kdx_dose_unit_id_real,
                                            'date_added' => date('Y-m-d H:i:s'),
                                            'date_updated' => date('Y-m-d H:i:s'),
                                        );
    
                                        $this->Kardex_stock_concoction->create($dt_kardex_stock_concoction);
                                    }
                                }
                            } else {
                                $kdx_stock_id = $kardex_stock[0]->id;
                                $kdx_stock_current_amount = $kardex_stock[0]->current_amount;
                                $kdx_stock_current_amount_unit_id = $kardex_stock[0]->current_amount_unit_id;
                                $kdx_stock_current_physical_quantity = $kardex_stock[0]->current_physical_quantity;
                            }
    
                            $tmp_current_amount = $this->Kardex_stock->count_amount($kdx_stock_current_amount, $pharmacy_qty, $kdx_catalog_packaging_value);
                            $current_physical_quantity = $kdx_stock_current_physical_quantity + $pharmacy_qty;
    
                            Logger::debug($this->debug_uniq, "current_amount: $tmp_current_amount");
                            Logger::debug($this->debug_uniq, "current_physical_quantity: $current_physical_quantity");
    
                            $dt_kardex_stock = array(
                                'signatura_times_id' => $kdx_signatura_times_id,
                                'dose' => $kdx_dose,
                                'dose_unit_id' => $kdx_dose_unit_id,
                                'current_amount' => $tmp_current_amount,
                                'current_physical_quantity' => $current_physical_quantity,
                                'date_updated' => date('Y-m-d H:i:s'),
                            );
                            $this->Kardex_stock->update_v2($kdx_stock_id, $dt_kardex_stock);
                        } else {
                            $select = 'a.id';
                            $where = array(
                                'a.registration_destination_id' => $kdx_registration_destination_id,
                                'a.parent_pharmacy_catalog_id' => $parent_prescription_type_pharmacy_catalog_id,
                                'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
                            );
                            $kdx_stock_concoction = $this->Kardex_stock_concoction->select(0, $select, $where);
                            if (count($kdx_stock_concoction) == 0) {
                                $dt_kardex_stock_concoction = array(
                                    'registration_destination_id' => $kdx_registration_destination_id,
                                    'parent_pharmacy_catalog_id' => $parent_prescription_type_pharmacy_catalog_id,
                                    'pharmacy_catalog_id' => $pharmacy_catalog_id,
                                    'dose' => $kdx_dose_real,
                                    'dose_unit_id' => $kdx_dose_unit_id_real,
                                    'date_added' => date('Y-m-d H:i:s'),
                                    'date_updated' => date('Y-m-d H:i:s'),
                                );

                                $this->Kardex_stock_concoction->create($dt_kardex_stock_concoction);
                            }
                        }
                    }

                    if (($dt_put['form_prescription'][$i]['pharmacy_catalog_id_sub'] ?? null)) {
                        $catalog = $this->Catalog->read($dt_put['form_prescription'][$i]['pharmacy_catalog_id_sub'],false)[0];
                    } else {
                        $catalog = $this->Catalog->read($dt_put['form_prescription'][$i]['pharmacy_catalog_id'],false)[0];
                    }

                    $this->Assets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                    $this->Assets->whereFields['pharmacy_catalog_id'] = $catalog->id;
                    $assets = $this->Assets->read(0,false);
                    if($assets != []) {
                        // P0-4: qty_awal dari data dispensing, qty_retur dari histori baris ini
                        // (kolom terpisah, tidak ditimpa replace()).
                        // 4C-03D: harga satuan dari snapshot server-side (price_sale), bukan client.
                        $qty_awal   = (float) $dt_put['form_prescription'][$i]['qty'];
                        $qty_retur  = (float) $prescription_item[$i]->qty_retur;
                        $harga_sat  = $this->_c3d_price_of($prescription_item[$i], $user_id, $branch, $price_changes);

                        if ($qty_awal < 0 || $qty_retur < 0 || $qty_retur > $qty_awal) {
                            $this->REST_Return(422, 'qty_retur tidak boleh negatif atau melebihi qty', array(
                                'pharmacy_catalog_id' => $catalog->id,
                                'qty'                 => $qty_awal,
                                'qty_retur'           => $qty_retur,
                            ));
                            return;
                        }

                        $dt_prescription_item = array(
                            'pharmacy_prescription_id'      => $id,
                            'pharmacy_catalog_id'           => $catalog->id,
                            'prescription_item_type_id'     => $dt_put['form_prescription'][$i]['prescription_type_id'],
                            'package_id'                    => ($dt_put['form_prescription'][$i]['package_id'] ?? '') !== '' ? $dt_put['form_prescription'][$i]['package_id'] : $catalog->package_id,
                            'package_name'                  => $dt_put['form_prescription'][$i]['package_name'],
                            'dose'                          => $dt_put['form_prescription'][$i]['dose'],
                            'batch'                         => $assets[0]->batch,
                            'expired'                       => $assets[0]->expired_date,
                            'dose_doctor'                   => $dt_put['form_prescription'][$i]['sum_dose'],
                            'qty'                           => $dt_put['form_prescription'][$i]['qty'],
                            'qty_copy'                      => $dt_put['form_prescription'][$i]['qty_copy'],
                            'var_1'                         => $harga_sat,
                            // P0-4: nilai baris dihitung server; var_2 kiriman client diabaikan.
                            'var_2'                         => round(max(0, $qty_awal - $qty_retur) * $harga_sat, 2),
                            // 4C-03D: HPP dari snapshot server, bukan var_3 client.
                            'var_3'                         => $prescription_item[$i]->price_hpp,
                            'sort'                          => $dt_put['form_prescription'][$i]['sort'],
                            'used'                          => $dt_put['form_prescription'][$i]['used'],
                            'desc'                          => $dt_put['form_prescription'][$i]['desc'],
                        );
                        // echo "<pre>";
                        // var_dump($dt_prescription_item);
                        // echo "<pre>";
                        $this->Prescription_item->replace($dt_prescription_item);

                        // 4C-02 R1: pengurangan stok atomik. Baris sudah dikunci sejak pra-validasi;
                        // guard di WHERE membuat stok tidak mungkin negatif walau ada penulis lain.
                        $qty_now = (float) $dt_put['form_prescription'][$i]['qty'];
                        $stok_after = $this->_c2_decrement_stock($dt_put['pharmacy_depo_id'], $catalog->id, $qty_now);
                        if ($stok_after === false) {
                            $this->db->trans_rollback();
                            $stok_error = array(
                                'reason'                => 'STOCK_INSUFFICIENT',
                                'pharmacy_catalog_id'   => $catalog->id,
                                'required'              => $qty_now,
                                'phase'                 => 'write',
                            );
                            $this->_c2_audit('DENIED_STOCK_INSUFFICIENT', $id, $user_id, $stok_error, 409);
                            $this->REST_Return(409, 'Stok tidak mencukupi.', $stok_error, 'STOCK_INSUFFICIENT');
                            return;
                        }
                        $prev_amount     = $stok_after['prev_amount'];
                        $physical_amount = $stok_after['physical_amount'];
                        $admin_amount    = $stok_after['admin_amount'];

                        // Pharmacy_assets_card log
                        $dt_pharmacy_assets_card = array(
                            'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                            'pharmacy_catalog_id'   => $catalog->id,
                            'reff_id'               => $id,
                            'batch'                 => $assets[0]->batch,
                            'expired_date'          => $assets[0]->expired_date,
                            'prev_amount'           => $prev_amount,
                            'in_amount'             => 0,
                            'out_amount'            => $dt_put['form_prescription'][$i]['qty'],
                            'current_amount'        => $physical_amount,
                            // 4C-03D: nilai kartu stok dari snapshot server, bukan payload client.
                            'hna_item'              => $prescription_item[$i]->price_purchase,
                            'hp_item'               => $prescription_item[$i]->price_hpp,
                            'hja_item'              => $prescription_item[$i]->price_sale,
                            'flag'                  => 3,
                            'date_created'          => date('Y-m-d H:i:s',time()),                                    
                        );
                        $this->Assets_card->create($dt_pharmacy_assets_card);
                    } else {
                        $this->Assets->replace(array(
                            'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                            'pharmacy_catalog_id'   => $catalog->id,
                            'prev_amount'           => 0,
                            'physical_amount'       => 0,
                            'admin_amount'          => 0,
                            'date_updated'          => date('Y-m-d H:i:s',time()),                                    
                        ));

                        $this->Assets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                        $this->Assets->whereFields['pharmacy_catalog_id'] = $catalog->id;
                        $assets = $this->Assets->read(0,false);
                        
                        // P0-4: nilai baris dihitung server (qty_awal - qty_retur histori) x harga.
                        // 4C-03D: harga satuan dari snapshot server-side, bukan client.
                        $harga_sat2 = $this->_c3d_price_of($prescription_item[$i], $user_id, $branch, $price_changes);
                        $dt_prescription_item = array(
                            'pharmacy_prescription_id'      => $id,
                            'pharmacy_catalog_id'           => $catalog->id,
                            'prescription_item_type_id'     => $dt_put['form_prescription'][$i]['prescription_type_id'],
                            'package_id'                    => ($dt_put['form_prescription'][$i]['package_id'] ?? '') !== '' ? $dt_put['form_prescription'][$i]['package_id'] : $catalog->package_id,
                            'package_name'                  => $dt_put['form_prescription'][$i]['package_name'],
                            'dose'                          => $dt_put['form_prescription'][$i]['dose'],
                            'batch'                         => $assets[0]->batch,
                            'expired'                       => $assets[0]->expired_date,
                            'dose_doctor'                   => $dt_put['form_prescription'][$i]['sum_dose'],
                            'qty'                           => $dt_put['form_prescription'][$i]['qty'],
                            'qty_copy'                      => $dt_put['form_prescription'][$i]['qty_copy'],
                            'var_1'                         => $harga_sat2,
                            'var_2'                         => round(max(0, (float) $dt_put['form_prescription'][$i]['qty'] - (float) $prescription_item[$i]->qty_retur) * $harga_sat2, 2),
                            // 4C-03D: HPP dari snapshot server, bukan var_3 client.
                            'var_3'                         => $prescription_item[$i]->price_hpp,
                            'sort'                          => $dt_put['form_prescription'][$i]['sort'],
                            'used'                          => $dt_put['form_prescription'][$i]['used'],
                            'desc'                          => $dt_put['form_prescription'][$i]['desc'],
                        );
                        // echo "<pre>";
                        // var_dump($dt_prescription_item);
                        // echo "<pre>";
                        // die();
                        $this->Prescription_item->replace($dt_prescription_item);

                        // 4C-02 R1: jalur "tidak ada baris stok" tidak lagi memberi stok negatif.
                        // qty > 0 tanpa baris pharmacy_assets sudah ditolak di pra-validasi; kalau
                        // sampai di sini, qty = 0 (tidak ada pergerakan stok) atau baris stok baru
                        // saja terbentuk. Guard tetap dipasang supaya tidak pernah negatif.
                        $qty_now = (float) $dt_put['form_prescription'][$i]['qty'];
                        $stok_after = $this->_c2_decrement_stock($dt_put['pharmacy_depo_id'], $catalog->id, $qty_now);
                        if ($stok_after === false) {
                            $this->db->trans_rollback();
                            $stok_error = array(
                                'reason'                => 'STOCK_INSUFFICIENT',
                                'pharmacy_catalog_id'   => $catalog->id,
                                'required'              => $qty_now,
                                'phase'                 => 'write',
                            );
                            $this->_c2_audit('DENIED_STOCK_INSUFFICIENT', $id, $user_id, $stok_error, 409);
                            $this->REST_Return(409, 'Stok tidak mencukupi.', $stok_error, 'STOCK_INSUFFICIENT');
                            return;
                        }
                        $prev_amount     = $stok_after['prev_amount'];
                        $physical_amount = $stok_after['physical_amount'];
                        $admin_amount    = $stok_after['admin_amount'];

                        // Pharmacy_assets_card log
                        $dt_pharmacy_assets_card = array(
                            'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                            'pharmacy_catalog_id'   => $catalog->id,
                            'reff_id'               => $id,
                            'batch'                 => $assets[0]->batch,
                            'expired_date'          => $assets[0]->expired_date,
                            'prev_amount'           => $prev_amount,
                            'in_amount'             => 0,
                            'out_amount'            => $dt_put['form_prescription'][$i]['qty'],
                            'current_amount'        => $physical_amount,
                            // 4C-03D: nilai kartu stok dari snapshot server, bukan payload client.
                            'hna_item'              => $prescription_item[$i]->price_purchase,
                            'hp_item'               => $prescription_item[$i]->price_hpp,
                            'hja_item'              => $prescription_item[$i]->price_sale,
                            'flag'                  => 3,
                            'date_created'          => date('Y-m-d H:i:s',time()),                                    
                        );
                        $this->Assets_card->create($dt_pharmacy_assets_card);
                    }
                }
                } catch (\Throwable $e) {
                    $this->db->trans_rollback();
                    $this->_c2_audit('DISPENSE_ROLLBACK', $id, $user_id, array(
                        'reason' => 'exception',
                        'error'  => get_class($e) . ': ' . $e->getMessage(),
                    ), 500);
                    $this->REST_Return(500, 'Penyerahan gagal dan dibatalkan.', array(), 'DISPENSE_ROLLBACK');
                    return;
                }

                // die();

                // 4C-03D: setiap perubahan harga pada baris resep dicatat (audit),
                // dengan old_price/new_price/snapshot_date/applied_by/applied_from_branch.
                // Gagal tulis audit => transaksi dibatalkan (fail-closed).
                if (count($price_changes) > 0) {
                    if (!$this->_c2_audit('PRICE_APPLIED', $id, $user_id, array(
                        'price_source'        => 'pharmacy_prescription_item.price_sale',
                        'applied_by'          => $user_id,
                        'applied_from_branch' => $branch,
                        'changes'             => $price_changes,
                    ), 200)) {
                        $this->db->trans_rollback();
                        $this->REST_Return(500, 'Gagal menulis audit perubahan harga.', array(), 'AUDIT_WRITE_FAILED');
                        return;
                    }
                }

                // P0-4: total header dihitung server dari item yang baru ditulis (lihat _bill_total).
                $dt_header_total = $this->_bill_total($id);
                $this->db->query(
                    "UPDATE pharmacy_prescription SET var_1 = ?, var_3 = ? - IFNULL(var_2,0) WHERE id = ?",
                    array($dt_header_total, $dt_header_total, $id)
                );

                // 4C-02: audit sukses ditulis DI DALAM transaksi, jadi barisnya hanya ada
                // kalau commit benar-benar berhasil (tidak pernah "sukses" palsu).
                // Gagal tulis audit => transaksi dibatalkan (fail-closed), sama seperti P1-b.
                if (!$this->_c2_audit('PRESCRIPTION_DISPENSED', $id, $user_id, array(
                    'items'         => count($prescription_item),
                    'total'         => $dt_header_total,
                    'depo_id'       => $dt_put['pharmacy_depo_id'],
                    'price_source'  => 'snapshot',
                    'price_changes' => count($price_changes),
                ), 200)) {
                    $this->db->trans_rollback();
                    $this->REST_Return(500, 'Gagal menulis audit penyerahan.', array(), 'AUDIT_WRITE_FAILED');
                    return;
                }

                // Tahap 1: jurnal otomatis penyerahan obat (debit HPP / credit persediaan).
                // Nilai = total header server-side ($dt_header_total), bukan angka dari client.
                $journal = $this->post_journal('PHARMACY_DISPENSE', $id, 'RX-DISP-' . $id,
                    $prescription[0]->institute_branch_id, $user_id,
                    array('hpp' => $dt_header_total, 'stock' => $dt_header_total),
                    'no_rawat=' . (isset($prescription[0]->no_rawat) ? $prescription[0]->no_rawat : ''));
                if (isset($journal['status']) && $journal['status'] === 'error') {
                    $this->db->trans_rollback();
                    $this->REST_Return(500, 'Jurnal penyerahan gagal (tidak balance).', $journal, 'JOURNAL_FAILED');
                    return;
                }

                $this->db->trans_complete();
                /* End Transaction */
                
                if ($this->db->trans_status() === false) {
                    // Transaksi sudah di-rollback oleh trans_complete(): audit kegagalan ditulis
                    // di luar transaksi supaya tetap tercatat.
                    $this->_c2_audit('DISPENSE_ROLLBACK', $id, $user_id, array('reason' => 'trans_status_false'), 422);
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $dt_reg = $this->Registration_destination->read($prescription[0]->registration_destination_id,false)[0];
                    if ($dt_reg->code_booking && $dt_reg->sts == 5) {
                        $dt_Registration = array(
                            'id' => $dt_reg->id,
                            'registration_id' => $dt_reg->registration_id,
                            'health_facility_item_id' => $dt_reg->health_facility_item_id,
                            'sts' => 6,
                            'date_task_6' => date('Y-m-d H:i:s'),
                            'call_pharmacy_stamp' => date('Y-m-d H:i:s')
                        );
                        $this->Registration_destination->replace($dt_Registration);
                        $update_antrean_6 = array(
                            'kodebooking'       => $prescription[0]->code_booking,
                            'taskid'            => 6,
                            'waktu'             => $this->tomiliseconds(date("Y-m-d H:i:s"))
                        );
                        $this->load->library('Vclaim_antrol','vclaim_antrol');
                        $updatewaktu_antrean_6 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_6));
                        $json_update_antrean_6 = json_decode($updatewaktu_antrean_6, true);
                        $json_update_antrean_6 = json_decode($json_update_antrean_6);
                        // jika gagal update antrean bpjs
                        // var_dump($json_update_antrean_6);
                        if($json_update_antrean_6->metadata->code != 200) {
                            // rollback
                            $this->db->trans_rollback();
                            $this->REST_Return(422, $json_update_antrean_6->metadata->message, $json_update_antrean_6->metadata->message);
                            return;
                        }
                    }
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

    public function transactiondone_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // 4C-05: scope branch fail-closed (objek harus milik branch token).
        $ph_branch = $this->_ph_branch('write');
        if ($ph_branch === null) { return; }
        if (!$this->_ph_object_scope($id, $ph_branch, 'write')) { return; }
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('sign_patient', 'sign_patient', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $prescription = $this->$model->read($id);
                $dt_reg = $this->Registration_destination->read($prescription[0]->registration_destination_id,false)[0];

                // Get raw POST input
                $rawInput = file_get_contents('php://input');

                // Decode JSON to array
                $request_data = json_decode($rawInput, true);
                $base64WithPrefix_1 = $request_data['sign_patient'] ?? '';
                $base64_1 = preg_replace('/^data:image\/\w+;base64,/', '', $base64WithPrefix_1);

                $extension = explode('/', $request_data['extension'])[1];
                $filename_1 = $prescription[0]->no_order . '_' . date('Ymd') . '.' . $extension;
                
                $url_1 = $this->base64ToFileNew($base64_1, $filename_1, 'assets/document/registration/patient_signature/');

                // var_dump($prescription[0]->code_booking);
                // die();
                $dt_prescription = array(
                    'id'                        => $id,
                    'sign_patient'              => $url_1,
                    'is_chronic'                => $request_data['is_chronic'],
                    'is_taskid'                 => 1
                );
                // echo "<pre>";
                // var_dump($dt_prescription);
                // echo "<pre>";
                // die();
                $this->$model->replace($dt_prescription);
                
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    if ($dt_reg->code_booking && $dt_reg->sts == 6) {
                        $dt_Registration = array(
                            'id' => $dt_reg->id,
                            'registration_id' => $dt_reg->registration_id,
                            'health_facility_item_id' => $dt_reg->health_facility_item_id,
                            'sts' => 7,
                            'date_task_7' => date('Y-m-d H:i:s'),
                            'done_pharmacy_stamp' => date('Y-m-d H:i:s')
                        );
                        $this->Registration_destination->replace($dt_Registration);
                        $update_antrean_7 = array(
                            'kodebooking'       => $dt_reg->code_booking,
                            'taskid'            => 7,
                            'waktu'             => $this->tomiliseconds(date("Y-m-d H:i:s", strtotime('+1 minutes')))
                        );
                        $this->load->library('Vclaim_antrol','vclaim_antrol');
                        $updatewaktu_antrean_7 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_7));
                        // $json_update_antrean_7 = json_decode($updatewaktu_antrean_7, true);
                        // $json_update_antrean_7 = json_decode($json_update_antrean_7);
                        // jika gagal update antrean bpjs
                        // var_dump($json_update_antrean_7);
                        // if($json_update_antrean_7->metadata->code != 200) {
                        //     // rollback
                        //     $this->db->trans_rollback();
                        //     $this->REST_Return(422, $json_update_antrean_7->metadata->message, $json_update_antrean_7->metadata->message);
                        //     return;
                        // }
                    }
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

    private function base64ToFileNew ($p = 0, $filename = '', $upload_dir = 'assets/document/registration/patient_signature/') {
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = $upload_dir . $file_name;
        $success      = file_put_contents($file, $data);
        return $file;
    }

    private function tomiliseconds($tanggal)
    {
        date_default_timezone_set('Asia/Jakarta');
        return strtotime($tanggal) * 1000;
    }

    /* ==========================================================================
     * 4C-04 — RETUR FARMASI: request -> telaah (approve/reject) -> stok kembali
     * ==========================================================================
     * Alur (mengikuti Khanza, dipadatkan ke 3 status):
     *   POST returrequest/:id   pasien/poli/ranap mengajukan retur   -> pending
     *   PUT  returapprove/:id   telaah farmasi menyetujui            -> approved + stok masuk
     *   PUT  returreject/:id    telaah farmasi menolak               -> rejected, stok tetap
     *   GET  returstatus/:id    status per item untuk form FE
     * Guard: branch dari token (fail-closed), baris resep & stok dikunci FOR UPDATE,
     * stok hanya bertambah di approve, qty_retur <= qty yang diserahkan, dan
     * approve/reject pada baris yang sudah diproses ditolak (idempotensi).
     */

    /** GET returstatus/:id — daftar status retur per item (read-only). */
    public function returstatus_get($p = 0) {
        $id = $this->security->xss_clean($p);
        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) OR $is_valid_token['status'] !== TRUE) {
            $this->REST_Return(401, isset($is_valid_token['message']) ? $is_valid_token['message'] : 'Invalid Token.', 'Invalid Token.');
            return;
        }
        $branch = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        if (trim((string) $branch) === '') {
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return;
        }

        $q = $this->db->query(
            "SELECT i.pharmacy_catalog_id, c.name AS pharmacy_catalog_name, i.qty, IFNULL(i.qty_retur,0) AS qty_retur,
                    i.retur_status, i.retur_qty_requested, i.retur_reason,
                    i.retur_requested_by, i.retur_requested_at, i.retur_approved_by, i.retur_approved_at,
                    i.is_retur, i.is_retur_full, p.flag, p.no_order, p.pharmacy_depo_id, p.institute_branch_id
               FROM pharmacy_prescription_item i
               JOIN pharmacy_prescription p ON p.id = i.pharmacy_prescription_id
               LEFT JOIN pharmacy_catalog c ON c.id = i.pharmacy_catalog_id
              WHERE i.pharmacy_prescription_id = ? AND i.is_delete = 0
              ORDER BY i.sort",
            array($id));
        $rows = ($q === FALSE) ? array() : $q->result();

        if (count($rows) === 0) {
            $this->REST_Return(404, 'Prescription with id ' . $id . ' does not exist!');
            return;
        }
        // Scope: branch resep harus sama dengan branch token (fail-closed).
        if ((string) $rows[0]->institute_branch_id !== (string) $branch) {
            $this->REST_Return(403, 'Resep berada di luar branch scope.', array(), 'BRANCH_SCOPE_MISMATCH');
            return;
        }
        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'no_resep'            => $r->no_order,
                'pharmacy_catalog_id' => $r->pharmacy_catalog_id,
                'kode_obat'           => $r->pharmacy_catalog_name,
                'qty'                 => (float) $r->qty,
                'qty_retur'           => (float) $r->qty_retur,
                'sisa_bisa_retur'     => max(0, (float) $r->qty - ((int) $r->is_retur === 1 ? (float) $r->qty_retur : 0)),
                'status'              => ($r->retur_status === null || $r->retur_status === '') ? 'none' : $r->retur_status,
                'qty_diminta'         => ($r->retur_qty_requested === null) ? null : (float) $r->retur_qty_requested,
                'reason'              => $r->retur_reason,
                'requested_by'        => $r->retur_requested_by,
                'requested_at'        => $r->retur_requested_at,
                'approved_by'         => $r->retur_approved_by,
                'approved_at'         => $r->retur_approved_at,
                'source_depo'         => $r->pharmacy_depo_id,
            );
        }
        $this->REST_Return(200, 'SUCCESS', $out);
        return;
    }

    /** POST returrequest/:id — ajukan retur (tidak mengubah stok maupun tagihan). */
    public function returrequest_post($p = 0) {
        $id = $this->security->xss_clean($p);
        $data = $this->json_post();
        $dt_post = $data['dt_postput'];

        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) OR $is_valid_token['status'] !== TRUE) {
            $this->REST_Return(401, isset($is_valid_token['message']) ? $is_valid_token['message'] : 'Invalid Token.', 'Invalid Token.');
            return;
        }
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $branch  = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        if (trim((string) $branch) === '') {
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return;
        }
        $rows = isset($dt_post['form_prescription']) ? $dt_post['form_prescription'] : array();
        if (!is_array($rows) || count($rows) === 0) {
            $this->REST_Return(422, 'form_prescription wajib diisi.', array(), 'RETUR_ITEMS_REQUIRED');
            return;
        }

        $this->db->trans_start();
        $rx = $this->_c4_lock_rx($id, $branch, $user_id, 'request');
        if ($rx === null) {
            return;
        }
        $res = $this->_c4_mark_pending($rx, $user_id, $rows);
        if ($res['ok'] !== true) {
            $this->_c4_deny($res['code'], $id, $user_id, $res, 409, 'Permintaan retur ditolak.');
            return;
        }
        if (!$this->_c2_audit('RETUR_REQUESTED', $id, $user_id, array(
            'no_resep'     => $rx->no_order,
            'source_depo'  => $rx->pharmacy_depo_id,
            'target_depo'  => $rx->pharmacy_depo_id,
            'applied_by'   => $user_id,
            'applied_from_branch' => $branch,
            'items'        => $res['items'],
        ), 200)) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit permintaan retur.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Permintaan retur gagal dan dibatalkan.', array(), 'RETUR_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array('prescription_id' => $id, 'status' => 'pending', 'items' => $res['items']));
        return;
    }

    /** PUT returapprove/:id — setujui retur; stok masuk ke depo asal (satu-satunya titik tulis stok). */
    public function returapprove_put($p = 0) {
        $id = $this->security->xss_clean($p);
        $data = $this->json_put();
        $dt_put = $data['dt_postput'];

        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) OR $is_valid_token['status'] !== TRUE) {
            $this->REST_Return(401, isset($is_valid_token['message']) ? $is_valid_token['message'] : 'Invalid Token.', 'Invalid Token.');
            return;
        }
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $branch  = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        if (trim((string) $branch) === '') {
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return;
        }
        $depo_id = isset($dt_put['pharmacy_depo_id']) ? $this->security->xss_clean($dt_put['pharmacy_depo_id']) : '';

        $this->db->trans_start();
        $rx = $this->_c4_lock_rx($id, $branch, $user_id, 'approve');
        if ($rx === null) {
            return;
        }
        $res = $this->_c4_approve_pending($rx, $user_id, $depo_id);
        if ($res['ok'] !== true) {
            $this->_c4_deny($res['code'], $id, $user_id, $res, 409, 'Persetujuan retur ditolak.');
            return;
        }

        $dt_header_total = $this->_bill_total($id);
        $this->db->query(
            "UPDATE pharmacy_prescription SET var_1 = ?, var_3 = ? - IFNULL(var_2,0), is_retur = 1, user_retur = ? WHERE id = ?",
            array($dt_header_total, $dt_header_total, $user_id, $id));

        if (!$this->_c2_audit('RETUR_APPROVED', $id, $user_id, array(
            'no_resep'    => $rx->no_order,
            'source_depo' => $rx->pharmacy_depo_id,
            'target_depo' => $rx->pharmacy_depo_id,
            'approved_by' => $user_id,
            'approved_at' => date('Y-m-d H:i:s'),
            'applied_from_branch' => $branch,
            'total_setelah_retur' => $dt_header_total,
            'items'       => $res['items'],
        ), 200)) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit persetujuan retur.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Persetujuan retur gagal dan dibatalkan.', array(), 'RETUR_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'prescription_id' => $id, 'status' => 'approved',
            'total' => $dt_header_total, 'items' => $res['items'],
        ));
        return;
    }

    /** PUT returreject/:id — tolak retur; tidak ada perubahan stok maupun tagihan. */
    public function returreject_put($p = 0) {
        $id = $this->security->xss_clean($p);
        $data = $this->json_put();
        $dt_put = $data['dt_postput'];

        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) OR $is_valid_token['status'] !== TRUE) {
            $this->REST_Return(401, isset($is_valid_token['message']) ? $is_valid_token['message'] : 'Invalid Token.', 'Invalid Token.');
            return;
        }
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $branch  = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        if (trim((string) $branch) === '') {
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return;
        }
        $reason = isset($dt_put['reason']) ? trim((string) $dt_put['reason']) : '';

        $this->db->trans_start();
        $rx = $this->_c4_lock_rx($id, $branch, $user_id, 'reject');
        if ($rx === null) {
            return;
        }
        $items = $this->_c4_items_locked($id);
        $pending = array();
        foreach ($items as $it) {
            if ((string) $it->retur_status === 'pending') {
                $pending[] = $it;
            }
        }
        if (count($pending) === 0) {
            $this->_c4_deny('RETUR_NOT_PENDING', $id, $user_id, array('reason' => 'no_pending_retur'), 409,
                'Tidak ada permintaan retur yang menunggu telaah.');
            return;
        }
        $now = date('Y-m-d H:i:s');
        $detail = array();
        foreach ($pending as $it) {
            $this->db->query(
                "UPDATE pharmacy_prescription_item
                    SET retur_status = 'rejected', retur_reason = ?, retur_approved_by = ?, retur_approved_at = ?
                  WHERE pharmacy_prescription_id = ? AND pharmacy_catalog_id = ?",
                array(($reason === '' ? $it->retur_reason : $reason), $user_id, $now,
                      $rx->id, $it->pharmacy_catalog_id));
            $detail[] = array(
                'no_resep'            => $rx->no_order,
                'kode_obat'           => $it->pharmacy_catalog_id,
                'qty_retur'           => (float) $it->retur_qty_requested,
                'reason'              => ($reason === '' ? $it->retur_reason : $reason),
                'approved_by'         => $user_id,
                'approved_at'         => $now,
                'old_stok'            => null,
                'new_stok'            => null,
                'source_depo'         => $rx->pharmacy_depo_id,
                'target_depo'         => null,
            );
        }
        if (!$this->_c2_audit('RETUR_REJECTED', $id, $user_id, array(
            'no_resep'    => $rx->no_order,
            'reason'      => $reason,
            'approved_by' => $user_id,
            'approved_at' => $now,
            'applied_from_branch' => $branch,
            'items'       => $detail,
        ), 200)) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit penolakan retur.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Penolakan retur gagal dan dibatalkan.', array(), 'RETUR_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array('prescription_id' => $id, 'status' => 'rejected', 'items' => $detail));
        return;
    }

    /**
     * 4C-04 — kunci baris resep + guard scope/status. Return null bila ditolak
     * (respons sudah dikirim + audit ditulis).
     */
    protected function _c4_lock_rx($id, $branch, $user_id, $operation) {
        $q = $this->db->query(
            "SELECT id, no_order, flag, is_delete, is_retur, institute_branch_id, pharmacy_depo_id,
                    registration_destination_id, var_1, var_2, var_3
               FROM pharmacy_prescription WHERE id = ? FOR UPDATE",
            array($id));
        $rx = ($q === FALSE) ? null : $q->row();
        if ($rx === null) {
            $this->_c4_deny('RETUR_RX_NOT_FOUND', $id, $user_id, array('reason' => 'not_found', 'operation' => $operation), 404,
                'Prescription with id ' . $id . ' does not exist!');
            return null;
        }
        if ((int) $rx->is_delete === 1) {
            $this->_c4_deny('RETUR_RX_DELETED', $id, $user_id, array('reason' => 'is_delete', 'operation' => $operation), 409,
                'Resep sudah dihapus.');
            return null;
        }
        // Retur hanya untuk obat yang benar-benar sudah diserahkan.
        if ((int) $rx->flag !== 1) {
            $this->_c4_deny('RETUR_NOT_DISPENSED', $id, $user_id, array('reason' => 'flag_not_1', 'flag' => $rx->flag, 'operation' => $operation), 409,
                'Resep belum diserahkan, tidak ada obat yang bisa diretur.');
            return null;
        }
        if ((string) $rx->institute_branch_id !== (string) $branch) {
            $this->_c4_deny('RETUR_BRANCH_SCOPE_MISMATCH', $id, $user_id, array(
                'reason' => 'branch_mismatch', 'rx_branch' => $rx->institute_branch_id,
                'token_branch' => $branch, 'operation' => $operation), 403,
                'Resep berada di luar branch scope.');
            return null;
        }
        return $rx;
    }

    /** 4C-04 — baris item resep, dikunci FOR UPDATE (urutan deterministik). */
    protected function _c4_items_locked($id) {
        $q = $this->db->query(
            "SELECT * FROM pharmacy_prescription_item
              WHERE pharmacy_prescription_id = ? AND is_delete = 0 ORDER BY pharmacy_catalog_id FOR UPDATE",
            array($id));
        return ($q === FALSE) ? array() : $q->result();
    }

    /** 4C-04 — tandai item sebagai pending (tanpa menyentuh stok/tagihan). */
    protected function _c4_mark_pending($rx, $user_id, $rows) {
        $items = $this->_c4_items_locked($rx->id);
        $by_cat = array();
        foreach ($items as $it) {
            $by_cat[$it->pharmacy_catalog_id] = $it;
        }
        $now = date('Y-m-d H:i:s');
        $detail = array();
        foreach ($rows as $row) {
            $cat = isset($row['pharmacy_catalog_id']) ? trim((string) $row['pharmacy_catalog_id']) : '';
            $qty = isset($row['qty_retur']) ? $row['qty_retur'] : null;
            $reason = isset($row['reason']) ? trim((string) $row['reason']) : (isset($row['retur_reason']) ? trim((string) $row['retur_reason']) : '');
            if (!is_numeric($qty) || (float) $qty <= 0) {
                return array('ok' => false, 'code' => 'RETUR_QTY_INVALID', 'kode_obat' => $cat, 'qty_retur' => $qty);
            }
            if (!isset($by_cat[$cat])) {
                return array('ok' => false, 'code' => 'RETUR_ITEM_NOT_FOUND', 'kode_obat' => $cat);
            }
            $it = $by_cat[$cat];
            if ((string) $it->retur_status === 'pending') {
                return array('ok' => false, 'code' => 'RETUR_ALREADY_PENDING', 'kode_obat' => $cat);
            }
            if ((string) $it->retur_status === 'approved') {
                return array('ok' => false, 'code' => 'RETUR_ALREADY_PROCESSED', 'kode_obat' => $cat);
            }
            $returned = ((int) $it->is_retur === 1) ? (float) $it->qty_retur : 0.0;
            $avail    = (float) $it->qty - $returned;
            if ((float) $qty > $avail) {
                return array('ok' => false, 'code' => 'RETUR_QTY_EXCEEDS_DISPENSED', 'kode_obat' => $cat,
                             'qty' => (float) $it->qty, 'qty_retur' => (float) $qty, 'sisa' => $avail);
            }
            $this->db->query(
                "UPDATE pharmacy_prescription_item
                    SET retur_status = 'pending', retur_qty_requested = ?, retur_reason = ?,
                        retur_requested_by = ?, retur_requested_at = ?,
                        retur_approved_by = NULL, retur_approved_at = NULL
                  WHERE pharmacy_prescription_id = ? AND pharmacy_catalog_id = ?",
                array((float) $qty, $reason, $user_id, $now, $rx->id, $cat));
            $detail[] = array(
                'no_resep'    => $rx->no_order,
                'kode_obat'   => $cat,
                'qty_retur'   => (float) $qty,
                'reason'      => $reason,
                'requested_by' => $user_id,
                'requested_at' => $now,
                'sisa_bisa_retur' => $avail,
                'source_depo' => $rx->pharmacy_depo_id,
                'target_depo' => $rx->pharmacy_depo_id,
            );
        }
        return array('ok' => true, 'items' => $detail);
    }

    /**
     * 4C-04 — setujui semua baris pending: kunci stok FOR UPDATE, tambah stok,
     * tulis qty_retur + audit detail. Ini satu-satunya tempat stok retur bertambah.
     */
    protected function _c4_approve_pending($rx, $user_id, $depo_id) {
        $target = trim((string) $depo_id) === '' ? (string) $rx->pharmacy_depo_id : trim((string) $depo_id);
        // Pemulangan stok hanya ke depo asal penyerahan (bukan depo lain / pusat).
        if ($target !== (string) $rx->pharmacy_depo_id) {
            return array('ok' => false, 'code' => 'RETUR_DEPO_MISMATCH',
                         'source_depo' => $rx->pharmacy_depo_id, 'target_depo' => $target);
        }

        $items = $this->_c4_items_locked($rx->id);
        $pending = array();
        foreach ($items as $it) {
            if ((string) $it->retur_status === 'pending') {
                $pending[] = $it;
            }
        }
        if (count($pending) === 0) {
            return array('ok' => false, 'code' => 'RETUR_NOT_PENDING', 'reason' => 'no_pending_retur');
        }

        $now = date('Y-m-d H:i:s');
        $detail = array();
        foreach ($pending as $it) {
            $cat = $it->pharmacy_catalog_id;
            $qty = (float) $it->retur_qty_requested;
            if ($qty <= 0) {
                return array('ok' => false, 'code' => 'RETUR_QTY_INVALID', 'kode_obat' => $cat, 'qty_retur' => $qty);
            }
            $returned = ((int) $it->is_retur === 1) ? (float) $it->qty_retur : 0.0;
            $avail    = (float) $it->qty - $returned;
            if ($qty > $avail) {
                return array('ok' => false, 'code' => 'RETUR_QTY_EXCEEDS_DISPENSED', 'kode_obat' => $cat,
                             'qty' => (float) $it->qty, 'qty_retur' => $qty, 'sisa' => $avail);
            }

            $stock = $this->_c4_add_stock($target, $cat, $qty);
            if ($stock === false) {
                return array('ok' => false, 'code' => 'RETUR_STOCK_WRITE_FAILED', 'kode_obat' => $cat);
            }

            $new_returned = $returned + $qty;
            $is_full      = ($new_returned >= (float) $it->qty) ? 1 : 0;
            // Harga retur = snapshot server-side pada baris resep (4C-03D), bukan payload.
            $price = ($it->price_sale !== null) ? (float) $it->price_sale : (float) $it->var_1;

            $this->db->query(
                "UPDATE pharmacy_prescription_item
                    SET qty_retur = ?, is_retur = 1, is_retur_full = ?, var_1 = ?,
                        var_2 = ROUND(GREATEST(IFNULL(qty,0) - ?, 0) * ?, 2),
                        retur_status = 'approved', retur_approved_by = ?, retur_approved_at = ?
                  WHERE pharmacy_prescription_id = ? AND pharmacy_catalog_id = ?",
                array($new_returned, $is_full, $price, $new_returned, $price, $user_id, $now, $rx->id, $cat));

            // Kartu stok: nilai dari snapshot server, bukan payload client.
            $this->Assets_card->create(array(
                'pharmacy_depo_id'    => $target,
                'pharmacy_catalog_id' => $cat,
                'reff_id'             => $rx->id,
                'batch'               => $it->batch,
                'expired_date'        => $it->expired,
                'prev_amount'         => $stock['prev'],
                'in_amount'           => $qty,
                'out_amount'          => 0,
                'current_amount'      => $stock['new'],
                'hna_item'            => ($it->price_purchase === null) ? null : (float) $it->price_purchase,
                'hp_item'             => ($it->price_hpp === null) ? null : (float) $it->price_hpp,
                'hja_item'            => $price,
                'flag'                => 3,
                'date_created'        => $now,
            ));

            $detail[] = array(
                'no_resep'    => $rx->no_order,
                'kode_obat'   => $cat,
                'qty_retur'   => $qty,
                'reason'      => $it->retur_reason,
                'approved_by' => $user_id,
                'approved_at' => $now,
                'old_stok'    => $stock['prev'],
                'new_stok'    => $stock['new'],
                'source_depo' => $rx->pharmacy_depo_id,
                'target_depo' => $target,
            );
        }
        return array('ok' => true, 'items' => $detail);
    }

    /**
     * 4C-04 — tambah stok atomik dengan baris dikunci FOR UPDATE.
     * Satu statement UPDATE (bukan baca-lalu-tulis) sehingga dua approve paralel
     * tidak saling menimpa. Baris stok dibuat bila belum ada.
     *
     * @return array|false array('prev'=>x,'new'=>y) atau false bila gagal.
     */
    protected function _c4_add_stock($depo_id, $catalog_id, $qty) {
        $qty = (float) $qty;
        $q = $this->db->query(
            "SELECT admin_amount, physical_amount FROM pharmacy_assets
              WHERE pharmacy_depo_id = ? AND pharmacy_catalog_id = ? FOR UPDATE",
            array($depo_id, $catalog_id));
        $row = ($q === FALSE) ? null : $q->row();
        if ($row === null) {
            $this->Assets->replace(array(
                'pharmacy_depo_id'    => $depo_id,
                'pharmacy_catalog_id' => $catalog_id,
                'prev_amount'         => 0,
                'physical_amount'     => 0,
                'admin_amount'        => 0,
                'date_updated'        => date('Y-m-d H:i:s'),
            ));
            $q = $this->db->query(
                "SELECT admin_amount, physical_amount FROM pharmacy_assets
                  WHERE pharmacy_depo_id = ? AND pharmacy_catalog_id = ? FOR UPDATE",
                array($depo_id, $catalog_id));
            $row = ($q === FALSE) ? null : $q->row();
            if ($row === null) {
                return false;
            }
        }
        $prev = (float) $row->admin_amount;
        $this->db->query(
            "UPDATE pharmacy_assets
                SET prev_amount = admin_amount,
                    admin_amount = admin_amount + ?,
                    physical_amount = physical_amount + ?,
                    date_updated = ?
              WHERE pharmacy_depo_id = ? AND pharmacy_catalog_id = ?",
            array($qty, $qty, date('Y-m-d H:i:s'), $depo_id, $catalog_id));
        if ($this->db->affected_rows() !== 1) {
            return false;
        }
        return array('prev' => $prev, 'new' => $prev + $qty);
    }

    /** 4C-04 — rollback + audit penolakan retur (dipakai semua jalur retur). */
    protected function _c4_deny($code, $id, $user_id, $detail, $http, $message) {
        $this->db->trans_rollback();
        $this->_c2_audit('DENIED_' . $code, $id, $user_id, array_merge(array(
            'applied_by'          => $user_id,
            'applied_from_branch' => isset($this->s1_actor['branch']) ? $this->s1_actor['branch'] : null,
            'old_stok'            => null,
            'new_stok'            => null,
        ), is_array($detail) ? $detail : array()), $http);
        $this->REST_Return($http, $message, is_array($detail) ? $detail : array(), $code);
    }

    public function retur_put($p = 0) {
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
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            // 4C-04: branch hanya dari token; kosong => fail-closed sebelum tulisan apa pun.
            $branch = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if (trim((string) $branch) === '') {
                $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
                return;
            }
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id', 'trim|required|max_length[36]');
            // $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('flag', 'flag', 'trim|max_length[1]');
            $this->form_validation->set_rules('form_prescription[]', 'form_prescription', 'trim|required|max_length[8]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // 4C-04: kunci baris resep + guard branch/flag/is_delete sebelum tulisan apa pun.
                $rx_locked = $this->_c4_lock_rx($id, $branch, $user_id, 'legacy');
                if ($rx_locked === null) {
                    return;
                }
                $prescription = $this->$model->read($id);
                if($prescription != []) {
                    if($this->Depo->read($dt_put['pharmacy_depo_id'], false) != []) {
                        // 4C-04: retur hanya ke depo asal penyerahan.
                        if ((string) $dt_put['pharmacy_depo_id'] !== (string) $rx_locked->pharmacy_depo_id) {
                            $this->_c4_deny('RETUR_DEPO_MISMATCH', $id, $user_id, array(
                                'source_depo' => $rx_locked->pharmacy_depo_id,
                                'target_depo' => $dt_put['pharmacy_depo_id'],
                            ), 409, 'Retur hanya boleh dikembalikan ke depo asal penyerahan.');
                            return;
                        }
                        // P0-4: validasi qty_retur SEMUA item sebelum satu baris pun ditulis.
                        // qty_retur negatif atau melebihi qty_awal ditolak (422), bukan diabaikan.
                        // 4C-04: baris yang sudah diretur tidak bisa diretur lagi (idempotensi).
                        $this->Prescription_item->whereFields['a.is_delete'] = 0;
                        $prescription_item_check = $this->Prescription_item->read($id,false);
                        for ($v=0; $v < count($prescription_item_check); $v++) {
                            $qty_retur_check = (float) $dt_put['form_prescription'][$v]['qty_retur'];
                            $qty_awal_check  = (float) $prescription_item_check[$v]->qty;
                            $sudah_retur     = ((int) $prescription_item_check[$v]->is_retur === 1)
                                ? (float) $prescription_item_check[$v]->qty_retur : 0.0;
                            if ($qty_retur_check > 0 && (int) $prescription_item_check[$v]->is_retur === 1) {
                                $this->_c4_deny('RETUR_ALREADY_PROCESSED', $id, $user_id, array(
                                    'kode_obat' => $prescription_item_check[$v]->pharmacy_catalog_id,
                                    'qty_retur' => $qty_retur_check,
                                    'sudah_retur' => $sudah_retur,
                                ), 409, 'Item ini sudah pernah diretur.');
                                return;
                            }
                            if ($qty_retur_check < 0 || $qty_retur_check > ($qty_awal_check - $sudah_retur)) {
                                $this->REST_Return(422, 'qty_retur tidak boleh negatif atau melebihi qty', array(
                                    'pharmacy_catalog_id' => $prescription_item_check[$v]->pharmacy_catalog_id,
                                    'qty'                 => $qty_awal_check,
                                    'qty_retur'           => $qty_retur_check,
                                ));
                                return;
                            }
                        }

                        $dt_prescription = array(
                            'id'                        => $id,
                            'pharmacy_depo_id'          => $dt_put['pharmacy_depo_id'],
                            // P0-4: nilai tagihan header dihitung ulang dari item setelah retur
                            // (lihat _bill_total); nilai kiriman client tidak dipakai.
                            'var_1'                     => $prescription[0]->var_1,
                            'var_2'                     => $prescription[0]->var_2,
                            'var_3'                     => $prescription[0]->var_3,
                            'flag'                      => 1,
                            'is_retur'                  => 1,
                            'user_retur'                => $user_id,
                            'date_1'                    => date("Y-m-d H:i:s",time()),
                        );
                        // echo "<pre>";
                        // var_dump($dt_prescription);
                        // echo "<pre>";
                        // die();
                        $this->$model->replace($dt_prescription);
                        $this->Prescription_item->whereFields['a.is_delete'] = 0;
                        $prescription_item = $this->Prescription_item->read($id,false);
                        for ($i=0; $i < count($prescription_item); $i++) { 
                            if ($dt_put['form_prescription'][$i]['qty_retur'] > 0 && $dt_put['form_prescription'][$i]['is_retur'] == 0) {
                                // P0-4: qty_retur tidak boleh negatif atau melebihi qty_awal.
                                $qty_awal_retur  = (float) $prescription_item[$i]->qty;
                                $qty_retur_input = (float) $dt_put['form_prescription'][$i]['qty_retur'];
                                // 4C-04: harga retur = snapshot server-side pada baris resep (4C-03D).
                                $harga_sat_retur = ($prescription_item[$i]->price_sale === null)
                                    ? max(0, (float) $prescription_item[$i]->var_1)
                                    : (float) $prescription_item[$i]->price_sale;

                                if ($qty_retur_input < 0 || $qty_retur_input > $qty_awal_retur) {
                                    $this->REST_Return(422, 'qty_retur tidak boleh negatif atau melebihi qty', array(
                                        'pharmacy_catalog_id' => $prescription_item[$i]->pharmacy_catalog_id,
                                        'qty'                 => $qty_awal_retur,
                                        'qty_retur'           => $qty_retur_input,
                                    ));
                                    return;
                                }

                                if (($dt_put['form_prescription'][$i]['pharmacy_catalog_id_sub'] ?? null)) {
                                    $catalog = $this->Catalog->read($dt_put['form_prescription'][$i]['pharmacy_catalog_id_sub'],false)[0];
                                } else {
                                    $catalog = $this->Catalog->read($dt_put['form_prescription'][$i]['pharmacy_catalog_id'],false)[0];
                                }
                                $this->Assets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                                $this->Assets->whereFields['pharmacy_catalog_id'] = $catalog->id;
                                $assets = $this->Assets->read(0,false);
                                if($assets != []) {
                                    $dt_prescription_item = array(
                                        'pharmacy_prescription_id'      => $id,
                                        'pharmacy_catalog_id'           => $catalog->id,
                                        'qty'                           => $dt_put['form_prescription'][$i]['qty'],
                                        'qty_copy'                      => $dt_put['form_prescription'][$i]['qty_copy'],
                                        'qty_retur'                     => $dt_put['form_prescription'][$i]['qty_retur'],
                                        'is_retur_full'                 => $dt_put['form_prescription'][$i]['qty'] == $dt_put['form_prescription'][$i]['qty_retur'] ? 1 : 0,
                                        'is_retur'                      => 1,
                                        // 4C-04: status retur ikut dicatat (approved, karena stok sudah dikembalikan).
                                        'retur_status'                  => 'approved',
                                        'retur_qty_requested'           => $qty_retur_input,
                                        'retur_approved_by'             => $user_id,
                                        'retur_approved_at'             => date('Y-m-d H:i:s'),
                                        'var_1'                         => $harga_sat_retur,
                                        // P0-4: nilai baris setelah retur dihitung server; var_2 client diabaikan.
                                        'var_2'                         => round(max(0, $qty_awal_retur - $qty_retur_input) * $harga_sat_retur, 2),
                                        'var_3'                         => $dt_put['form_prescription'][$i]['var_3'],
                                        'sort'                          => $dt_put['form_prescription'][$i]['sort'],
                                        'used'                          => $dt_put['form_prescription'][$i]['used'],
                                        'desc'                          => $dt_put['form_prescription'][$i]['desc'],
                                    );
                                    // echo "<pre>";
                                    // var_dump($dt_prescription_item);
                                    // echo "<pre>";
                                    $this->Prescription_item->replace($dt_prescription_item);

                                    // 4C-04: tambah stok atomik (baris dikunci FOR UPDATE), bukan baca-lalu-tulis.
                                    $retur_qty_now = (float) $dt_put['form_prescription'][$i]['qty_retur'];
                                    $stok_retur = $this->_c4_add_stock($dt_put['pharmacy_depo_id'], $catalog->id, $retur_qty_now);
                                    if ($stok_retur === false) {
                                        $this->_c4_deny('RETUR_STOCK_WRITE_FAILED', $id, $user_id, array(
                                            'kode_obat' => $catalog->id, 'qty_retur' => $retur_qty_now), 409,
                                            'Pemulangan stok gagal.');
                                        return;
                                    }
                                    $prev_amount     = $stok_retur['prev'];
                                    $physical_amount = $stok_retur['new'];
                                    $admin_amount    = $stok_retur['new'];

                                    // Pharmacy_assets_card log (nilai dari snapshot server, bukan payload).
                                    $dt_pharmacy_assets_card = array(
                                        'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'reff_id'               => $id,
                                        'batch'                 => NULL,
                                        'expired_date'          => NULL,
                                        'prev_amount'           => $prev_amount,
                                        'in_amount'             => $retur_qty_now,
                                        'out_amount'            => 0,
                                        'current_amount'        => $physical_amount,
                                        'hna_item'              => ($prescription_item[$i]->price_purchase === null) ? null : (float) $prescription_item[$i]->price_purchase,
                                        'hp_item'               => ($prescription_item[$i]->price_hpp === null) ? null : (float) $prescription_item[$i]->price_hpp,
                                        'hja_item'              => $harga_sat_retur,
                                        'flag'                  => 3,
                                        'date_created'          => date('Y-m-d H:i:s',time()),
                                    );
                                    $this->Assets_card->create($dt_pharmacy_assets_card);
                                } else {
                                    $this->Assets->replace(array(
                                        'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'prev_amount'           => 0,
                                        'physical_amount'       => 0,
                                        'admin_amount'          => 0,
                                        'date_updated'          => date('Y-m-d H:i:s',time()),                                    
                                    ));
                                    $this->Assets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                                    $this->Assets->whereFields['pharmacy_catalog_id'] = $catalog->id;
                                    $assets = $this->Assets->read(0,false);
                                    $dt_prescription_item = array(
                                        'pharmacy_prescription_id'      => $id,
                                        'pharmacy_catalog_id'           => $catalog->id,
                                        'prescription_item_type_id'     => $dt_put['form_prescription'][$i]['prescription_type_id'],
                                        'package_id'                    => ($dt_put['form_prescription'][$i]['package_id'] ?? '') !== '' ? $dt_put['form_prescription'][$i]['package_id'] : $catalog->package_id,
                                        'package_name'                  => $dt_put['form_prescription'][$i]['package_name'],
                                        'dose'                          => $dt_put['form_prescription'][$i]['dose'],
                                        'batch'                         => $assets[0]->batch,
                                        'expired'                       => $assets[0]->expired_date,
                                        'dose_doctor'                   => $dt_put['form_prescription'][$i]['sum_dose'],
                                        'qty'                           => $dt_put['form_prescription'][$i]['qty'],
                                        'qty_copy'                      => $dt_put['form_prescription'][$i]['qty_copy'],
                                        'qty_retur'                     => $qty_retur_input,
                                        'is_retur'                      => 1,
                                        'is_retur_full'                 => ($qty_retur_input >= $qty_awal_retur) ? 1 : 0,
                                        'retur_status'                  => 'approved',
                                        'retur_qty_requested'           => $qty_retur_input,
                                        'retur_approved_by'             => $user_id,
                                        'retur_approved_at'             => date('Y-m-d H:i:s'),
                                        'var_1'                         => $harga_sat_retur,
                                        // P0-4: nilai baris setelah retur dihitung server; var_2 client diabaikan.
                                        'var_2'                         => round(max(0, $qty_awal_retur - $qty_retur_input) * $harga_sat_retur, 2),
                                        'var_3'                         => $dt_put['form_prescription'][$i]['var_3'],
                                        'sort'                          => $dt_put['form_prescription'][$i]['sort'],
                                        'used'                          => $dt_put['form_prescription'][$i]['used'],
                                        'desc'                          => $dt_put['form_prescription'][$i]['desc'],
                                    );
                                    // echo "<pre>";
                                    // var_dump($dt_prescription_item);
                                    // echo "<pre>";
                                    // die();
                                    $this->Prescription_item->replace($dt_prescription_item);

                                    // 4C-04: retur selalu MENAMBAH stok depo asal (atomik, FOR UPDATE).
                                    $retur_qty_now = (float) $dt_put['form_prescription'][$i]['qty_retur'];
                                    $stok_retur = $this->_c4_add_stock($dt_put['pharmacy_depo_id'], $catalog->id, $retur_qty_now);
                                    if ($stok_retur === false) {
                                        $this->_c4_deny('RETUR_STOCK_WRITE_FAILED', $id, $user_id, array(
                                            'kode_obat' => $catalog->id, 'qty_retur' => $retur_qty_now), 409,
                                            'Pemulangan stok gagal.');
                                        return;
                                    }
                                    $prev_amount     = $stok_retur['prev'];
                                    $physical_amount = $stok_retur['new'];
                                    $admin_amount    = $stok_retur['new'];

                                    // Pharmacy_assets_card log (nilai dari snapshot server, bukan payload).
                                    $dt_pharmacy_assets_card = array(
                                        'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'reff_id'               => $id,
                                        'batch'                 => $assets[0]->batch,
                                        'expired_date'          => $assets[0]->expired_date,
                                        'prev_amount'           => $prev_amount,
                                        'in_amount'             => $retur_qty_now,
                                        'out_amount'            => 0,
                                        'current_amount'        => $physical_amount,
                                        'hna_item'              => ($prescription_item[$i]->price_purchase === null) ? null : (float) $prescription_item[$i]->price_purchase,
                                        'hp_item'               => ($prescription_item[$i]->price_hpp === null) ? null : (float) $prescription_item[$i]->price_hpp,
                                        'hja_item'              => $harga_sat_retur,
                                        'flag'                  => 3,
                                        'date_created'          => date('Y-m-d H:i:s',time()),
                                    );
                                    $this->Assets_card->create($dt_pharmacy_assets_card);
                                }
                            // SYARAT GANTI CATALOG ADALAH RETUR PENUH PADA BARANG!
                            } else if ($dt_put['form_prescription'][$i]['pharmacy_catalog_id_ref'] && $dt_put['form_prescription'][$i]['is_retur_full'] == 1) {
                                $catalog = $this->Catalog->read($dt_put['form_prescription'][$i]['pharmacy_catalog_id'],false)[0];
                                $this->Assets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                                $this->Assets->whereFields['pharmacy_catalog_id'] = $catalog->id;
                                $assets = $this->Assets->read(0,false);
                                if($assets != []) {
                                    $dt_prescription_item = array(
                                        'pharmacy_prescription_id'      => $id,
                                        'pharmacy_catalog_id'           => $catalog->id,
                                        'prescription_item_type_id'     => $dt_put['form_prescription'][$i]['prescription_type_id'],
                                        'package_id'                    => ($dt_put['form_prescription'][$i]['package_id'] ?? '') !== '' ? $dt_put['form_prescription'][$i]['package_id'] : $catalog->package_id,
                                        'package_name'                  => $dt_put['form_prescription'][$i]['package_name'],
                                        'dose'                          => $dt_put['form_prescription'][$i]['dose'],
                                        'batch'                         => $assets[0]->batch,
                                        'expired'                       => $assets[0]->expired_date,
                                        'pharmacy_catalog_id_ref'       => NULL,
                                        'pharmacy_catalog_id_retur'     => $dt_put['form_prescription'][$i]['pharmacy_catalog_id_ref'],
                                        'dose_doctor'                   => $dt_put['form_prescription'][$i]['sum_dose'],
                                        'qty'                           => $dt_put['form_prescription'][$i]['qty'],
                                        'qty_copy'                      => $dt_put['form_prescription'][$i]['qty_copy'],
                                        'var_1'                         => $harga_sat_retur,
                                        // P0-4: nilai baris setelah retur dihitung server; var_2 client diabaikan.
                                        'var_2'                         => round(max(0, $qty_awal_retur - $qty_retur_input) * $harga_sat_retur, 2),
                                        'var_3'                         => $dt_put['form_prescription'][$i]['var_3'],
                                        'sort'                          => $dt_put['form_prescription'][$i]['sort'],
                                        'used'                          => $dt_put['form_prescription'][$i]['used'],
                                        'desc'                          => $dt_put['form_prescription'][$i]['desc'],
                                    );
                                    // 4C-04: debug var_dump dihapus (merusak body JSON respons).
                                    $this->Prescription_item->replace($dt_prescription_item);
    
                                    // 4C-04: pengurangan stok atomik (compare-and-set), bukan baca-lalu-tulis.
                                    $sub_qty = (float) $dt_put['form_prescription'][$i]['qty'];
                                    $stok_sub = $this->_c2_decrement_stock($dt_put['pharmacy_depo_id'], $catalog->id, $sub_qty);
                                    if ($stok_sub === false) {
                                        $this->_c4_deny('STOCK_INSUFFICIENT', $id, $user_id, array(
                                            'kode_obat' => $catalog->id, 'required' => $sub_qty), 409,
                                            'Stok tidak mencukupi untuk substitusi.');
                                        return;
                                    }
                                    $prev_amount     = $stok_sub['prev_amount'];
                                    $physical_amount = $stok_sub['physical_amount'];
                                    $admin_amount    = $stok_sub['admin_amount'];
    
                                    // check the catalog if it is not out of stock
                                    // if($physical_amount >= 0) {
                                        // Update pharmacy_assets stock
                                        $dt_pharmacy_assets = array(
                                            'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                                            'pharmacy_catalog_id'   => $catalog->id,
                                            'prev_amount'           => $prev_amount,
                                            'physical_amount'       => $physical_amount,
                                            'admin_amount'          => $admin_amount,
                                            'date_updated'          => date('Y-m-d H:i:s',time()),                                    
                                        );
    
                                        // if (date('Y-m-d', strtotime($dt_put['date_added'])) == date('Y-m-d')) {
                                            // echo "<pre>";
                                            // var_dump($dt_pharmacy_assets);
                                            // echo "<pre>";
                                            $this->Assets->replace($dt_pharmacy_assets);
                                        // }
        
                                        // Pharmacy_assets_card log
                                        $dt_pharmacy_assets_card = array(
                                            'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                                            'pharmacy_catalog_id'   => $catalog->id,
                                            'reff_id'               => $id,
                                            'batch'                 => $assets[0]->batch,
                                            'expired_date'          => $assets[0]->expired_date,
                                            'prev_amount'           => $prev_amount,
                                            'in_amount'             => 0,
                                            'out_amount'            => $dt_put['form_prescription'][$i]['qty'],
                                            'current_amount'        => $physical_amount,
                                            // 4C-04: nilai kartu stok dari snapshot server, bukan payload client.
                                            'hna_item'              => ($prescription_item[$i]->price_purchase === null) ? null : (float) $prescription_item[$i]->price_purchase,
                                            'hp_item'               => ($prescription_item[$i]->price_hpp === null) ? null : (float) $prescription_item[$i]->price_hpp,
                                            'hja_item'              => $harga_sat_retur,
                                            'flag'                  => 3,
                                            'date_created'          => date('Y-m-d H:i:s',time()),                                    
                                        );
                                        // if (date('Y-m-d', strtotime($dt_put['date_added'])) == date('Y-m-d')) {
                                            // echo "<pre>";
                                            // var_dump($dt_pharmacy_assets_card);
                                            // echo "<pre>";
                                            $this->Assets_card->create($dt_pharmacy_assets_card);
                                        // }
                                        // die();
                                    // kunci stok
                                    // } else {
                                    //     $this->REST_Return(400, 'There is only ' . $prev_amount . ' left for pharmacy_catalog_id with id ' . $catalog->id);
                                    //     return;
                                    // }
                                } else {
                                    $this->Assets->replace(array(
                                        'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'prev_amount'           => 0,
                                        'physical_amount'       => 0,
                                        'admin_amount'          => 0,
                                        'date_updated'          => date('Y-m-d H:i:s',time()),                                    
                                    ));
                                    $this->Assets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                                    $this->Assets->whereFields['pharmacy_catalog_id'] = $catalog->id;
                                    $assets = $this->Assets->read(0,false);
                                    $dt_prescription_item = array(
                                        'pharmacy_prescription_id'      => $id,
                                        'pharmacy_catalog_id'           => $catalog->id,
                                        'prescription_item_type_id'     => $dt_put['form_prescription'][$i]['prescription_type_id'],
                                        'package_id'                    => ($dt_put['form_prescription'][$i]['package_id'] ?? '') !== '' ? $dt_put['form_prescription'][$i]['package_id'] : $catalog->package_id,
                                        'package_name'                  => $dt_put['form_prescription'][$i]['package_name'],
                                        'dose'                          => $dt_put['form_prescription'][$i]['dose'],
                                        'batch'                         => $assets[0]->batch,
                                        'expired'                       => $assets[0]->expired_date,
                                        'dose_doctor'                   => $dt_put['form_prescription'][$i]['sum_dose'],
                                        'qty'                           => $dt_put['form_prescription'][$i]['qty'],
                                        'qty_copy'                      => $dt_put['form_prescription'][$i]['qty_copy'],
                                        'var_1'                         => $harga_sat_retur,
                                        // P0-4: nilai baris setelah retur dihitung server; var_2 client diabaikan.
                                        'var_2'                         => round(max(0, $qty_awal_retur - $qty_retur_input) * $harga_sat_retur, 2),
                                        'var_3'                         => $dt_put['form_prescription'][$i]['var_3'],
                                        'sort'                          => $dt_put['form_prescription'][$i]['sort'],
                                        'used'                          => $dt_put['form_prescription'][$i]['used'],
                                        'desc'                          => $dt_put['form_prescription'][$i]['desc'],
                                    );
                                    // echo "<pre>";
                                    // var_dump($dt_prescription_item);
                                    // echo "<pre>";
                                    // die();
                                    $this->Prescription_item->replace($dt_prescription_item);
    
                                    // 4C-04: pengurangan stok atomik (compare-and-set), bukan baca-lalu-tulis.
                                    $sub_qty = (float) $dt_put['form_prescription'][$i]['qty'];
                                    $stok_sub = $this->_c2_decrement_stock($dt_put['pharmacy_depo_id'], $catalog->id, $sub_qty);
                                    if ($stok_sub === false) {
                                        $this->_c4_deny('STOCK_INSUFFICIENT', $id, $user_id, array(
                                            'kode_obat' => $catalog->id, 'required' => $sub_qty), 409,
                                            'Stok tidak mencukupi untuk substitusi.');
                                        return;
                                    }
                                    $prev_amount     = $stok_sub['prev_amount'];
                                    $physical_amount = $stok_sub['physical_amount'];
                                    $admin_amount    = $stok_sub['admin_amount'];
    
                                    // check the catalog if it is not out of stock
                                    // if($physical_amount >= 0) {
                                        // Update pharmacy_assets stock
                                        $dt_pharmacy_assets = array(
                                            'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                                            'pharmacy_catalog_id'   => $catalog->id,
                                            'prev_amount'           => $prev_amount,
                                            'physical_amount'       => $physical_amount,
                                            'admin_amount'          => $admin_amount,
                                            'date_updated'          => date('Y-m-d H:i:s',time()),                                    
                                        );
    
                                        // if (date('Y-m-d', strtotime($dt_put['date_added'])) == date('Y-m-d')) {
                                            // echo "<pre>";
                                            // var_dump($dt_pharmacy_assets);
                                            // echo "<pre>";
                                            $this->Assets->replace($dt_pharmacy_assets);
                                        // }
        
                                        // Pharmacy_assets_card log
                                        $dt_pharmacy_assets_card = array(
                                            'pharmacy_depo_id'      => $dt_put['pharmacy_depo_id'],
                                            'pharmacy_catalog_id'   => $catalog->id,
                                            'reff_id'               => $id,
                                            'batch'                 => $assets[0]->batch,
                                            'expired_date'          => $assets[0]->expired_date,
                                            'prev_amount'           => $prev_amount,
                                            'in_amount'             => 0,
                                            'out_amount'            => $dt_put['form_prescription'][$i]['qty'],
                                            'current_amount'        => $physical_amount,
                                            // 4C-04: nilai kartu stok dari snapshot server, bukan payload client.
                                            'hna_item'              => ($prescription_item[$i]->price_purchase === null) ? null : (float) $prescription_item[$i]->price_purchase,
                                            'hp_item'               => ($prescription_item[$i]->price_hpp === null) ? null : (float) $prescription_item[$i]->price_hpp,
                                            'hja_item'              => $harga_sat_retur,
                                            'flag'                  => 3,
                                            'date_created'          => date('Y-m-d H:i:s',time()),                                    
                                        );
                                        // if (date('Y-m-d', strtotime($dt_put['date_added'])) == date('Y-m-d')) {
                                            // echo "<pre>";
                                            // var_dump($dt_pharmacy_assets_card);
                                            // echo "<pre>";
                                            $this->Assets_card->create($dt_pharmacy_assets_card);
                                        // }
                                        // die();
                                    // kunci stok
                                    // } else {
                                    //     $this->REST_Return(400, 'There is only ' . $prev_amount . ' left for pharmacy_catalog_id with id ' . $catalog->id);
                                    //     return;
                                    // }
                                }
                            }
                        }
                    } else {
                        $this->REST_Return(404, 'Wrong pharmacy_depo_id or soap_id!');
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Prescription with id ' . $id . ' does not exist!');
                    return;
                }

                // P0-4: total header dihitung server dari item (lihat _bill_total di akhir method).
                $dt_header_total = $this->_bill_total($id);
                $this->db->query(
                    "UPDATE pharmacy_prescription SET var_1 = ?, var_3 = ? - IFNULL(var_2,0) WHERE id = ?",
                    array($dt_header_total, $dt_header_total, $id)
                );

                // 4C-04: audit retur dengan field wajib (no_resep, kode_obat, qty_retur, reason,
                // approved_by/at, old_stok, new_stok, source_depo, target_depo).
                $retur_audit_items = array();
                foreach ($this->_c4_items_locked($id) as $it_audit) {
                    if ((int) $it_audit->is_retur !== 1) {
                        continue;
                    }
                    $st = $this->db->query(
                        "SELECT admin_amount FROM pharmacy_assets WHERE pharmacy_depo_id = ? AND pharmacy_catalog_id = ?",
                        array($dt_put['pharmacy_depo_id'], $it_audit->pharmacy_catalog_id))->row();
                    $new_stok = ($st === null) ? null : (float) $st->admin_amount;
                    $retur_audit_items[] = array(
                        'no_resep'    => $rx_locked->no_order,
                        'kode_obat'   => $it_audit->pharmacy_catalog_id,
                        'qty_retur'   => (float) $it_audit->qty_retur,
                        'reason'      => $it_audit->retur_reason,
                        'approved_by' => $user_id,
                        'approved_at' => $it_audit->retur_approved_at,
                        'old_stok'    => ($new_stok === null) ? null : $new_stok - (float) $it_audit->qty_retur,
                        'new_stok'    => $new_stok,
                        'source_depo' => $rx_locked->pharmacy_depo_id,
                        'target_depo' => $dt_put['pharmacy_depo_id'],
                    );
                }
                if (!$this->_c2_audit('RETUR_APPLIED', $id, $user_id, array(
                    'no_resep'            => $rx_locked->no_order,
                    'source_depo'         => $rx_locked->pharmacy_depo_id,
                    'target_depo'         => $dt_put['pharmacy_depo_id'],
                    'approved_by'         => $user_id,
                    'approved_at'         => date('Y-m-d H:i:s'),
                    'applied_from_branch' => $branch,
                    'total_setelah_retur' => $dt_header_total,
                    'items'               => $retur_audit_items,
                ), 200)) {
                    $this->db->trans_rollback();
                    $this->REST_Return(500, 'Gagal menulis audit retur.', array(), 'AUDIT_WRITE_FAILED');
                    return;
                }

                // E-5: retur obat dari pasien kembali ke persediaan.
                // Debit persediaan / credit HPP (membalik HPP penyerahan).
                // ref_no memuat total setelah retur supaya replay retur yang sama
                // tidak menambah jurnal, tetapi retur berikutnya tetap tercatat.
                $ret_val = $this->db->query(
                    "SELECT IFNULL(SUM(qty_retur * IFNULL(price_hpp,0)),0) AS amt
                       FROM pharmacy_prescription_item
                      WHERE pharmacy_prescription_id = ? AND IFNULL(qty_retur,0) > 0",
                    array($id))->row();
                $ret_total = $ret_val === null ? 0.0 : round((float) $ret_val->amt, 2);
                if ($ret_total > 0) {
                    $ref = 'RET-' . $id . '-' . str_replace('.', '_', number_format((float) $dt_header_total, 2, '.', ''));
                    $journal = $this->post_journal('STOCK_RETURN', $id, $ref,
                        $rx_locked->institute_branch_id, $user_id,
                        array('stock' => $ret_total, 'hpp' => $ret_total),
                        'no_resep=' . $rx_locked->no_order);
                    if (isset($journal['status']) && $journal['status'] === 'error') {
                        $this->db->trans_rollback();
                        $this->REST_Return(500, 'Jurnal retur gagal (tidak balance).', $journal, 'JOURNAL_FAILED');
                        return;
                    }
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

    public function add_post($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            // 4C-03D: branch hanya dari token; kosong => fail-closed sebelum tulisan apa pun.
            if (trim((string) $institute_branch_id) === '') {
                $this->_c2_audit('DENIED_BRANCH_SCOPE_UNDEFINED', $id, $user_id, array(
                    'reason' => 'branch_empty', 'operation' => 'add'), 409);
                $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
                return;
            }
            $this->form_validation->set_data($dt_post);
            # Form Validation
            if ($dt_post['type_prescription_id'] != 5) {
                $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id', 'trim|required|max_length[36]');
                $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
                $this->form_validation->set_rules('flag', 'flag', 'trim|max_length[1]');
            }
            $this->form_validation->set_rules('form_prescription[]', 'form_prescription', 'trim|required|max_length[8]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if($this->Depo->read($dt_post['pharmacy_depo_id'], false) != []) {
                    // 4C-03D: harga seluruh item dari pharmacy_catalog_price (server-side).
                    // Payload client (var_1/hja/hna/var_3) tidak dipakai. Satu item tanpa harga
                    // => rollback total, tidak ada resep/penyerahan yang ditulis.
                    $c3d_identity = $this->_c3c_identity($dt_post['registration_destination_id']);
                    $c3d_prices   = array();
                    for ($i = 0; $i < count($dt_post['form_prescription']); $i++) {
                        $res = $this->_c3c_resolve_price(
                            $dt_post['form_prescription'][$i]['pharmacy_catalog_id'],
                            $institute_branch_id, $dt_post['pharmacy_depo_id'],
                            $c3d_identity['service_class_id'], $c3d_identity['insurance_id'], date('Y-m-d'));
                        if (!$res['ok']) {
                            $this->db->trans_rollback();
                            $this->_c2_audit('DENIED_' . $res['code'], $id, $user_id, array(
                                'operation'           => 'add',
                                'index'               => $i,
                                'pharmacy_catalog_id' => $dt_post['form_prescription'][$i]['pharmacy_catalog_id'],
                                'old_price'           => null,
                                'new_price'           => null,
                                'snapshot_date'       => null,
                                'applied_by'          => $user_id,
                                'applied_from_branch' => $institute_branch_id,
                                'reason'              => $res['code'],
                            ), 409);
                            $this->REST_Return(409, 'Harga obat tidak ditemukan pada master harga.', array(
                                'index'               => $i,
                                'pharmacy_catalog_id' => $dt_post['form_prescription'][$i]['pharmacy_catalog_id'],
                            ), $res['code']);
                            return;
                        }
                        $c3d_prices[$i] = $this->_c3c_snapshot($res['row']);
                    }

                    // $this->Prescription->whereFields['a.date_added >='] = date('Y-m-d').' 00:00:00';
                    // $this->Prescription->whereFields['a.date_added <='] = date('Y-m-d').' 23:59:59';
                    // $this->Prescription->orderFields = array('a.no_order' => 'DESC');
                    // $this->Prescription->limit = 1;
                    // $tmp = $this->Prescription->read(0,false);
                    // $waktu = date("Ymd",time());
                    // if($tmp != []) {
                    //     $no_order = 'PRS' . $waktu . str_pad(intval(substr($tmp[0]->no_order,11)) + 1, 4, '0', STR_PAD_LEFT);
                    //     // Check if reset is needed
                    //     if(substr($tmp[0]->no_order,3,8) != date('Ymd',time())) {
                    //         $no_order = 'PRS' . $waktu . '0001';
                    //     }
                    // } else {
                    //     $no_order = 'PRS' . $waktu . '0001';
                    // }

                    // // Count queue
                    // $this->Prescription->whereFields['a.date_added >='] = date('Y-m-d').' 00:00:00';
                    // $this->Prescription->whereFields['a.date_added <='] = date('Y-m-d').' 23:59:59';
                    // $this->Prescription->orderFields = array('a.queue' => 'DESC');
                    // $this->Prescription->limit = 1;
                    // $tmp = $this->Prescription->read(0,false);
                    // if($tmp != []) {
                    //     $queue = $tmp[0]->queue + 1;
                    //     // Check if reset is needed
                    //     if(substr($tmp[0]->no_order,3,8) != date('Ymd',time())) {
                    //         $queue = 1;
                    //     }
                    // } else {
                    //     $queue = 1;
                    // }

                    // count no_order & queue
                    if ($dt_post['type_prescription_id'] == 5) {
                        $prefix = 'PRB';
                    } else {
                        $prefix = 'PRS';
                    }
                    $epoch = time();
                    $dt_queue = array(
                        'var_2' => date('Y', $epoch),
                        'var_3' => date('m', $epoch),
                        'var_4' => date('d', $epoch),
                        'var_5' => date('Y-m-d', $epoch),
                    );

                    $no_order = counter_queue($prefix, $dt_queue, 4);
                    $queue = round((int)substr($no_order, -4));

                    $dt_prescription = array(
                        'norm'                          => $dt_post['norm'],
                        'institute_branch_id'           => $institute_branch_id,
                        'type_prescription_id'          => $dt_post['type_prescription_id'],
                        'fullname'                      => $dt_post['fullname'],
                        'pharmacy_depo_id'              => $dt_post['pharmacy_depo_id'],
                        'registration_destination_id'   => $dt_post['registration_destination_id'],
                        'health_facility_item_id'       => $dt_post['health_facility_item_id'],
                        'doctor_in_charge_id'           => $dt_post['doctor_in_charge_id'],
                        'no_order'                      => $no_order,
                        'user_1'                        => $user_id,
                        // 4C-03D: nilai tagihan header dihitung server dari snapshot item
                        // (lihat _bill_total); var_1/var_2/var_3 client tidak dipakai.
                        'var_1'                         => 0,
                        'var_2'                         => 0,
                        'var_3'                         => 0,
                        'date_added'                    => date("Y-m-d H:i:s",time()),
                        'date_0'                        => date("Y-m-d H:i:s",time()),
                        'date_1'                        => date("Y-m-d H:i:s",time()),
                        'flag'                          => 1,
                        'queue'                         => $queue,
                    );
                    $id_prescription = $this->Prescription->create($dt_prescription);
                    $response_data = $id_prescription;
                    for ($i=0; $i < count($dt_post['form_prescription']); $i++) { 
                        $catalog = $this->Catalog->read($dt_post['form_prescription'][$i]['pharmacy_catalog_id'],false)[0];
                        $this->Assets->whereFields['pharmacy_depo_id'] = $dt_post['pharmacy_depo_id'];
                        $this->Assets->whereFields['pharmacy_catalog_id'] = $catalog->id;
                        $assets = $this->Assets->read(0,false);
                        if($assets != []) {
                            $dt_prescription_item = array(
                                'pharmacy_prescription_id'      => $id_prescription,
                                'pharmacy_catalog_id'           => $catalog->id,
                                'prescription_item_type_id'     => $dt_post['form_prescription'][$i]['prescription_type_id'],
                                'package_id'                    => $dt_post['form_prescription'][$i]['package_id'],
                                'package_name'                  => $dt_post['form_prescription'][$i]['package_name'],
                                'dose'                          => $dt_post['form_prescription'][$i]['dose'],
                                'batch'                         => $assets[0]->batch,
                                'expired'                       => $assets[0]->expired_date,
                                'dose_doctor'                   => $dt_post['form_prescription'][$i]['sum_dose'],
                                'qty'                           => $dt_post['form_prescription'][$i]['qty'],
                                'qty_copy'                      => $dt_post['form_prescription'][$i]['qty_copy'],
                                // 4C-03D: harga dari snapshot server, bukan payload client.
                                'var_1'                         => $c3d_prices[$i]['var_1'],
                                'var_2'                         => round(max(0, (float) $dt_post['form_prescription'][$i]['qty']) * (float) $c3d_prices[$i]['var_1'], 2),
                                'var_3'                         => $c3d_prices[$i]['price_hpp'],
                                'sort'                          => $dt_post['form_prescription'][$i]['sort'],
                                'used'                          => $dt_post['form_prescription'][$i]['used'],
                                'desc'                          => $dt_post['form_prescription'][$i]['desc'],
                            );
                            // echo "<pre>";
                            // var_dump($dt_prescription_item);
                            // echo "<pre>";
                            // 4C-03D: kolom snapshot server ikut ditulis pada baris resep.
                            $dt_prescription_item = array_merge($dt_prescription_item, $c3d_prices[$i]);
                            $this->Prescription_item->replace($dt_prescription_item);

                            $prev_amount = $assets[0]->admin_amount;
                            $physical_amount = $assets[0]->physical_amount - intval($dt_post['form_prescription'][$i]['qty']);
                            $admin_amount = $assets[0]->admin_amount - intval($dt_post['form_prescription'][$i]['qty']);

                            // check the catalog if it is not out of stock
                            // if($physical_amount >= 0) {
                                // Update pharmacy_assets stock
                                // var_dump($catalog->is_chronic);die();
                                if ($catalog->is_chronic == 2 && $prev_amount < intval($dt_post['form_prescription'][$i]['qty'])) {
                                    // Pharmacy_assets_card log
                                    $dt_pharmacy_assets_card = array(
                                        'pharmacy_depo_id'      => $dt_post['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'reff_id'               => $id_prescription,
                                        'batch'                 => $assets[0]->batch,
                                        'expired_date'          => $assets[0]->expired_date,
                                        'prev_amount'           => 0,
                                        'in_amount'             => 0,
                                        'out_amount'            => $dt_post['form_prescription'][$i]['qty'],
                                        'current_amount'        => $physical_amount,
                                        // 4C-03D: nilai kartu stok dari snapshot server, bukan payload client.
                                        'hna_item'              => $c3d_prices[$i]['price_purchase'],
                                        'hp_item'               => $c3d_prices[$i]['price_hpp'],
                                        'hja_item'              => $c3d_prices[$i]['price_sale'],
                                        'flag'                  => 6,
                                        'date_created'          => date('Y-m-d H:i:s',time()),                                    
                                    );
                                    // if (date('Y-m-d', strtotime($dt_post['date_added'])) == date('Y-m-d')) {
                                        // echo "<pre>";
                                        // var_dump($dt_pharmacy_assets_card);
                                        // echo "<pre>";
                                        $this->Assets_card->create($dt_pharmacy_assets_card);
                                    // }
                                } else {
                                    $dt_pharmacy_assets = array(
                                        'pharmacy_depo_id'      => $dt_post['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'prev_amount'           => $prev_amount,
                                        'physical_amount'       => $physical_amount,
                                        'admin_amount'          => $admin_amount,
                                        'date_updated'          => date('Y-m-d H:i:s',time()),                                    
                                    );

                                    // if (date('Y-m-d', strtotime($dt_post['date_added'])) == date('Y-m-d')) {
                                        // echo "<pre>";
                                        // var_dump($dt_pharmacy_assets);
                                        // echo "<pre>";
                                        $this->Assets->replace($dt_pharmacy_assets);
                                    // }

                                    // Pharmacy_assets_card log
                                    $dt_pharmacy_assets_card = array(
                                        'pharmacy_depo_id'      => $dt_post['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'reff_id'               => $id_prescription,
                                        'batch'                 => $assets[0]->batch,
                                        'expired_date'          => $assets[0]->expired_date,
                                        'prev_amount'           => $prev_amount,
                                        'in_amount'             => 0,
                                        'out_amount'            => $dt_post['form_prescription'][$i]['qty'],
                                        'current_amount'        => $physical_amount,
                                        // 4C-03D: nilai kartu stok dari snapshot server, bukan payload client.
                                        'hna_item'              => $c3d_prices[$i]['price_purchase'],
                                        'hp_item'               => $c3d_prices[$i]['price_hpp'],
                                        'hja_item'              => $c3d_prices[$i]['price_sale'],
                                        'flag'                  => 3,
                                        'date_created'          => date('Y-m-d H:i:s',time()),                                    
                                    );
                                    // if (date('Y-m-d', strtotime($dt_post['date_added'])) == date('Y-m-d')) {
                                        // echo "<pre>";
                                        // var_dump($dt_pharmacy_assets_card);
                                        // echo "<pre>";
                                        $this->Assets_card->create($dt_pharmacy_assets_card);
                                    // }
                                }
                                // die();
                            // kunci stok
                            // } else {
                            //     $this->REST_Return(400, 'There is only ' . $prev_amount . ' left for pharmacy_catalog_id with id ' . $catalog->id);
                            //     return;
                            // }
                        } else {
                            $this->Assets->replace(array(
                                'pharmacy_depo_id'      => $dt_post['pharmacy_depo_id'],
                                'pharmacy_catalog_id'   => $catalog->id,
                                'prev_amount'           => 0,
                                'physical_amount'       => 0,
                                'admin_amount'          => 0,
                                'date_updated'          => date('Y-m-d H:i:s',time()),                                    
                            ));
                            $catalog = $this->Catalog->read($dt_post['form_prescription'][$i]['pharmacy_catalog_id'],false)[0];
                            $this->Assets->whereFields['pharmacy_depo_id'] = $dt_post['pharmacy_depo_id'];
                            $this->Assets->whereFields['pharmacy_catalog_id'] = $catalog->id;
                            $assets = $this->Assets->read(0,false);
                            $dt_prescription_item = array(
                                'pharmacy_prescription_id'      => $id_prescription,
                                'pharmacy_catalog_id'           => $catalog->id,
                                'prescription_item_type_id'     => $dt_post['form_prescription'][$i]['prescription_type_id'],
                                'package_id'                    => $dt_post['form_prescription'][$i]['package_id'],
                                'package_name'                  => $dt_post['form_prescription'][$i]['package_name'],
                                'dose'                          => $dt_post['form_prescription'][$i]['dose'],
                                'batch'                         => $assets[0]->batch,
                                'expired'                       => $assets[0]->expired_date,
                                'dose_doctor'                   => $dt_post['form_prescription'][$i]['sum_dose'],
                                'qty'                           => $dt_post['form_prescription'][$i]['qty'],
                                'qty_copy'                      => $dt_post['form_prescription'][$i]['qty_copy'],
                                // 4C-03D: harga dari snapshot server, bukan payload client.
                                'var_1'                         => $c3d_prices[$i]['var_1'],
                                'var_2'                         => round(max(0, (float) $dt_post['form_prescription'][$i]['qty']) * (float) $c3d_prices[$i]['var_1'], 2),
                                'var_3'                         => $c3d_prices[$i]['price_hpp'],
                                'sort'                          => $dt_post['form_prescription'][$i]['sort'],
                                'used'                          => $dt_post['form_prescription'][$i]['used'],
                                'desc'                          => $dt_post['form_prescription'][$i]['desc'],
                            );
                            // echo "<pre>";
                            // var_dump($dt_prescription_item);
                            // echo "<pre>";
                            // 4C-03D: kolom snapshot server ikut ditulis pada baris resep.
                            $dt_prescription_item = array_merge($dt_prescription_item, $c3d_prices[$i]);
                            $this->Prescription_item->replace($dt_prescription_item);

                            $prev_amount = $assets[0]->admin_amount;
                            $physical_amount = $assets[0]->physical_amount - intval($dt_post['form_prescription'][$i]['qty']);
                            $admin_amount = $assets[0]->admin_amount - intval($dt_post['form_prescription'][$i]['qty']);

                            // check the catalog if it is not out of stock
                            // if($physical_amount >= 0) {
                                // Update pharmacy_assets stock
                                if ($catalog->is_chronic == 2 && $prev_amount < intval($dt_post['form_prescription'][$i]['qty'])) {
                                    // Pharmacy_assets_card log
                                    $dt_pharmacy_assets_card = array(
                                        'pharmacy_depo_id'      => $dt_post['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'reff_id'               => $id_prescription,
                                        'batch'                 => $assets[0]->batch,
                                        'expired_date'          => $assets[0]->expired_date,
                                        'prev_amount'           => 0,
                                        'in_amount'             => 0,
                                        'out_amount'            => $dt_post['form_prescription'][$i]['qty'],
                                        'current_amount'        => $physical_amount,
                                        // 4C-03D: nilai kartu stok dari snapshot server, bukan payload client.
                                        'hna_item'              => $c3d_prices[$i]['price_purchase'],
                                        'hp_item'               => $c3d_prices[$i]['price_hpp'],
                                        'hja_item'              => $c3d_prices[$i]['price_sale'],
                                        'flag'                  => 6,
                                        'date_created'          => date('Y-m-d H:i:s',time()),                                    
                                    );
                                    // if (date('Y-m-d', strtotime($dt_post['date_added'])) == date('Y-m-d')) {
                                        // echo "<pre>";
                                        // var_dump($dt_pharmacy_assets_card);
                                        // echo "<pre>";
                                        $this->Assets_card->create($dt_pharmacy_assets_card);
                                    // }
                                } else {
                                    $dt_pharmacy_assets = array(
                                        'pharmacy_depo_id'      => $dt_post['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'prev_amount'           => $prev_amount,
                                        'physical_amount'       => $physical_amount,
                                        'admin_amount'          => $admin_amount,
                                        'date_updated'          => date('Y-m-d H:i:s',time()),                                    
                                    );
    
                                    // if (date('Y-m-d', strtotime($dt_post['date_added'])) == date('Y-m-d')) {
                                        // echo "<pre>";
                                        // var_dump($dt_pharmacy_assets);
                                        // echo "<pre>";
                                        $this->Assets->replace($dt_pharmacy_assets);
                                    // }
                                    // Pharmacy_assets_card log
                                    $dt_pharmacy_assets_card = array(
                                        'pharmacy_depo_id'      => $dt_post['pharmacy_depo_id'],
                                        'pharmacy_catalog_id'   => $catalog->id,
                                        'reff_id'               => $id_prescription,
                                        'batch'                 => $assets[0]->batch,
                                        'expired_date'          => $assets[0]->expired_date,
                                        'prev_amount'           => $prev_amount,
                                        'in_amount'             => 0,
                                        'out_amount'            => $dt_post['form_prescription'][$i]['qty'],
                                        'current_amount'        => $physical_amount,
                                        // 4C-03D: nilai kartu stok dari snapshot server, bukan payload client.
                                        'hna_item'              => $c3d_prices[$i]['price_purchase'],
                                        'hp_item'               => $c3d_prices[$i]['price_hpp'],
                                        'hja_item'              => $c3d_prices[$i]['price_sale'],
                                        'flag'                  => 3,
                                        'date_created'          => date('Y-m-d H:i:s',time()),                                    
                                    );
                                    // if (date('Y-m-d', strtotime($dt_post['date_added'])) == date('Y-m-d')) {
                                        // echo "<pre>";
                                        // var_dump($dt_pharmacy_assets_card);
                                        // echo "<pre>";
                                        $this->Assets_card->create($dt_pharmacy_assets_card);
                                    // }
                                }

                                // die();
                            // kunci stok
                            // } else {
                            //     $this->REST_Return(400, 'There is only ' . $prev_amount . ' left for pharmacy_catalog_id with id ' . $catalog->id);
                            //     return;
                            // }
                            // $this->REST_Return(404, 'There is no pharmacy_assets with pharmacy_catalog_id with id ' . $catalog->id);
                            // return;
                        }
                    }
                    // 4C-03D: total header dari snapshot item server-side (bukan var_1 client).
                    $c3d_total = $this->_bill_total($id_prescription);
                    $this->db->query(
                        "UPDATE pharmacy_prescription SET var_1 = ?, var_3 = ? - IFNULL(var_2,0) WHERE id = ?",
                        array($c3d_total, $c3d_total, $id_prescription)
                    );
                } else {
                    $this->REST_Return(404, 'Wrong pharmacy_depo_id or soap_id!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    // 4C-03D: audit snapshot harga server (bukan harga client) setelah commit.
                    $c3d_items_audit = array();
                    foreach ($c3d_prices as $i => $p) {
                        $c3d_items_audit[] = array(
                            'pharmacy_catalog_id' => $dt_post['form_prescription'][$i]['pharmacy_catalog_id'],
                            'price_id'            => $p['price_id'],
                            'price_sale'          => $p['price_sale'],
                            'snapshot_date'       => $p['price_snapshot_at'],
                        );
                    }
                    $this->_c2_audit('PRICE_SNAPSHOT_OK', $id_prescription, $user_id, array(
                        'operation'           => 'add',
                        'price_source'        => 'pharmacy_catalog_price',
                        'applied_by'          => $user_id,
                        'applied_from_branch' => $institute_branch_id,
                        'items'               => $c3d_items_audit,
                    ), 200);
                    $response_data = $id_prescription;
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

    public function delete_delete($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);
        // 4C-05: scope branch fail-closed (objek harus milik branch token).
        $ph_branch = $this->_ph_branch('write');
        if ($ph_branch === null) { return; }
        if (!$this->_ph_object_scope($id, $ph_branch, 'write')) { return; }
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        /* Start Transaction */
        $this->db->trans_begin();

        $is_delete = $dt_put['is_delete'];
    
        $prescription_id = $this->$model->select_id($id);
    
        if ($prescription_id == "") {
            $this->REST_Return(404, 'there is no prescription with id ' . $id);
            return;
        }

        $select = 'a.pharmacy_prescription_id';
        $where = array('pharmacy_prescription_id' => $prescription_id);
        $prescription_item = $this->Prescription_item->select(0, $select, $where);

        if (count($prescription_item) > 0) {
            $dt_prescription_item = array(
                'is_delete' => $is_delete,
            );

            $this->Prescription_item->update($prescription_id, $dt_prescription_item);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $response_data = array(
                    'error'     => $this->db->error(),
                    'data'      => $dt_prescription_item,
                    'query'     => $this->db->last_query()
                );
                $this->REST_Return(500, 'error while trying to delete prescription_item', $response_data);
                return;
            }
        } 
        
        $dt_prescription = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($prescription_id, $dt_prescription);
    
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_prescription,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete prescription', $response_data);
            return;
        }

        $this->db->trans_commit();
        /* End Transaction */

        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_v2_get($p = 0){
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

        $limit = $q_strings['limit'] ?? 20;
        $offset = $q_strings['limit'] ?? 0;

        $this->$model->limit = $limit;
        $this->$model->offset = $offset;

        // 4C-05: daftar resep difilter branch dari token.
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        $where = array('a.institute_branch_id' => $ph_branch);
    
        $norm = $q_strings['norm'] ?? null;
        if ($norm != null) {
            $where['a.norm'] = $norm;
        }

        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;
        if ($registration_destination_id != null) {
            $where['a.registration_destination_id'] = $registration_destination_id;
        }

        $list_type = $q_strings['list_type'] ?? null;
        if ($list_type != null) {
            if ($list_type == 'kardex') {
                $where_prescription = "a.type_prescription_id IN (4,6)";
                $this->db->where($where_prescription);
            }
        }
    
        $select = 'a.*, e.fullname as doctor_in_charge_name, pd.name as pharmacy_depo_name';
        // $where = array();
        $join = array(
            array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
            array('pharmacy_depo pd', 'a.pharmacy_depo_id = pd.id', 'LEFT'),
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
            array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
            array('soap s', 'sp.soap_id = s.id', 'LEFT'),
            array('registration_destination rd2', 's.registration_destination_id = rd2.id', 'LEFT'),
        );
        $order = array(
            'a.date_added' => 'DESC'
        );
        $output = $this->$model->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($output); $i++) { 
            $pharmacy_prescription_id = $output[$i]->id;

            $select_item = '
                a.pharmacy_catalog_id, 
                pc.name as pharmacy_catalog_name, 
                pc.code as pharmacy_catalog_code,
                a.prescription_item_type_id, 
                a.prescription_item_type_used, 
                a.prescription_item_type_value,
                a.prescription_item_type_package,
                a.package_id,
                p.name as package_name, 
                a.signatura_times_id,
                a.administer_id,
                a.dose, 
                a.dose_unit_id, 
                u.name as dose_name, 
                a.qty, 
                a.sort, 
                a.used, 
                a.desc, 
                a.qty_doctor
            ';
            $where_item = array(
                'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
                'a.is_delete' => 0,
            );
            $join_item = array(
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('package p', 'pc.package_id = p.id', 'LEFT'),
                array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
            );
            $prescription_item = $this->Prescription_item->select(0, $select_item, $where_item, $join_item);

            $output[$i]->form_prescription = $prescription_item;

            $form_prescription_parent = [];

            //------------------- detail form_prescription_parent -//
            for ($l=0; $l < count($prescription_item); $l++) { 
                $parent_id = $prescription_item[$l]->prescription_item_type_id; 
                $parent_used = $prescription_item[$l]->prescription_item_type_used; 
                $parent_value = $prescription_item[$l]->prescription_item_type_value; 
                $parent_package = $prescription_item[$l]->prescription_item_type_package; 
                $parent_signatura_times_id = $prescription_item[$l]->signatura_times_id; 
                $parent_administer_id = $prescription_item[$l]->administer_id; 

                $dt_form_prescription_parent = array(
                    'id' => $parent_id,
                    'used' => $parent_used,
                    'value' => $parent_value,
                    'package' => $parent_package,
                    'signatura_times_id' => $parent_signatura_times_id,
                    'administer_id' => $parent_administer_id,
                );

                if (count($form_prescription_parent) == 0) {
                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                }

                $skip = 0;
                for ($m=0; $m < count($form_prescription_parent); $m++) { 
                    if ($form_prescription_parent[$m]['id'] == $parent_id) {
                        $skip = 1;
                    }
                }

                if ($skip == 0) {
                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                }
            }
            //----------------------------------------------------//

            $output[$i]->form_prescription_parent = $form_prescription_parent;
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

    public function list_bhp_get($p = 0){
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

        $limit = '20';
        $offset = '0';
    
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit;
            unset($q_strings['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset;
            unset($q_strings['offset']);
        }

        $norm = $q_strings['norm'] ?? null;

        // 4C-05: daftar BHP difilter branch dari token.
        $ph_branch = $this->_ph_branch('read');
        if ($ph_branch === null) { return; }
        $where = '';

        if ($norm == null) {
            $registration_id = $q_strings['registration_id'] ?? null;
            if ($registration_id == null) {
                $this->REST_Return(400, 'registration_id is required if norm is not provided');
                return;
            }

            $select = 'a.id, a.norm';
            $registration = $this->Registration->select($registration_id, $select);
            if (count($registration) == 0) {
                $this->REST_Return(404, 'there is no registration with id ' . $registration_id);
                return;
            }

            // 4C-05: OR dikurung supaya tidak melewati filter is_delete/branch.
            $where = "AND (rd.registration_id = '$registration_id' OR rd2.registration_id = '$registration_id')";
            
            $norm = $registration[0]->norm;
        }
    
        // $select = 'a.*, e.fullname as doctor_in_charge_name, pd.name as pharmacy_depo_name';
        // $where = array(
        //     'a.norm' => $norm,
        // );
        // $join = array(
        //     array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
        //     array('pharmacy_depo pd', 'a.pharmacy_depo_id = pd.id', 'LEFT'),
        //     array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
        //     array('soap_p sp', 'a.soap_p_id = sp.id', 'LEFT'),
        //     array('soap s', 'sp.soap_id = s.id', 'LEFT'),
        //     array('registration_destination rd2', 's.registration_destination_id = rd2.id', 'LEFT'),
        // );
        // $output = $this->$model->select(0, $select, $where, $join);

        $output = $this->db->query("
            SELECT full_a.*, e.fullname as doctor_in_charge_name, pd.name as pharmacy_depo_name
            FROM (
                SELECT id
                FROM pharmacy_prescription
                WHERE norm = '$norm' AND institute_branch_id = '" . $this->db->escape_str($ph_branch) . "'
            ) as a
            INNER JOIN pharmacy_prescription as full_a ON a.id = full_a.id
            LEFT JOIN employee e ON full_a.doctor_in_charge_id = e.id
            LEFT JOIN pharmacy_depo pd ON full_a.pharmacy_depo_id = pd.id
            LEFT JOIN registration_destination rd ON full_a.registration_destination_id = rd.id
            LEFT JOIN soap_p sp ON full_a.soap_p_id = sp.id
            LEFT JOIN soap s ON sp.soap_id = s.id
            LEFT JOIN registration_destination rd2 ON s.registration_destination_id = rd2.id
            WHERE full_a.is_delete = 0
            $where
            ORDER BY full_a.date_added DESC
            LIMIT $limit
            OFFSET $offset
        ")->result();

        for ($i=0; $i < count($output); $i++) { 
            $pharmacy_prescription_id = $output[$i]->id;

            $select_item = '
                a.pharmacy_catalog_id, 
                pc.name as pharmacy_catalog_name, 
                pc.code as pharmacy_catalog_code,
                a.prescription_item_type_id, 
                a.prescription_item_type_used, 
                a.prescription_item_type_value,
                a.package_id,
                a.package_id,
                a.package_name, 
                a.dose, 
                p.name as dose_name, 
                a.qty, 
                a.sort, 
                a.used, 
                a.desc, 
                a.qty_doctor
            ';
            $where_item = array(
                'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
                'a.is_delete' => 0,
            );
            $join_item = array(
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('package p', 'pc.dose_id = p.id', 'LEFT'),
            );
            $prescription_item = $this->Prescription_item->select(0, $select_item, $where_item, $join_item);

            $output[$i]->form_prescription = $prescription_item;

            $form_prescription_parent = [];

            //------------------- detail form_prescription_parent -//
            for ($l=0; $l < count($prescription_item); $l++) { 
                $parent_id = $prescription_item[$l]->prescription_item_type_id; 
                $parent_used = $prescription_item[$l]->prescription_item_type_used; 
                $parent_value = $prescription_item[$l]->prescription_item_type_value; 

                $dt_form_prescription_parent = array(
                    'id' => $parent_id,
                    'used' => $parent_used,
                    'value' => $parent_value,
                );

                if (count($form_prescription_parent) == 0) {
                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                }

                $skip = 0;
                for ($m=0; $m < count($form_prescription_parent); $m++) { 
                    if ($form_prescription_parent[$m]['id'] == $parent_id) {
                        $skip = 1;
                    }
                }

                if ($skip == 0) {
                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                }
            }
            //----------------------------------------------------//

            $output[$i]->form_prescription_parent = $form_prescription_parent;
        }

        // $this->$model->limit = null;
        // $this->$model->offset = null;
        // $select = 'count(*) as iTotal';
        // $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;

        $iTotal = $this->db->query("
            SELECT COUNT(*) AS iTotal
            FROM (
                SELECT id
                FROM pharmacy_prescription
                WHERE norm = '$norm' AND institute_branch_id = '" . $this->db->escape_str($ph_branch) . "'
            ) as a
            INNER JOIN pharmacy_prescription as full_a ON a.id = full_a.id
            LEFT JOIN employee e ON full_a.doctor_in_charge_id = e.id
            LEFT JOIN pharmacy_depo pd ON full_a.pharmacy_depo_id = pd.id
            LEFT JOIN registration_destination rd ON full_a.registration_destination_id = rd.id
            LEFT JOIN soap_p sp ON full_a.soap_p_id = sp.id
            LEFT JOIN soap s ON sp.soap_id = s.id
            LEFT JOIN registration_destination rd2 ON s.registration_destination_id = rd2.id
            WHERE full_a.is_delete = 0
            $where
        ")->result()[0]->iTotal;

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function change_catalog_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);
        // 4C-05: scope branch fail-closed (objek harus milik branch token).
        $ph_branch = $this->_ph_branch('write');
        if ($ph_branch === null) { return; }
        if (!$this->_ph_object_scope($id, $ph_branch, 'write')) { return; }

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
        
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->change_catalog_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $select = 'a.id, a.employee_id, e.fullname as employee_name';
        $where = array();
        $join = array(
            array('employee e', 'a.employee_id = e.id', 'INNER'),
        );
        $user = $this->User->select($user_id, $select);
        $user_employee_id = $user[0]->employee_id;
        $user_employee_name = $user[0]->employee_name;

        $prescription_id = $id;

        if ($prescription_id === 0 || $prescription_id === "0") {
            $this->REST_Return(400, 'prescription_id is required');
            return;
        }

        $select = 'a.id, a.registration_destination_id, a.norm';
        $prescription = $this->Prescription->select($prescription_id, $select);
        if (count($prescription) == 0) {
            $this->REST_Return(404, 'there is no prescription with id ' . $prescription_id);
            return;
        }

        $registration_destination_id = $prescription[0]->registration_destination_id;
        $norm = $prescription[0]->norm;

        $pharmacy_catalog_id  = $dt_put['pharmacy_catalog_id'];
        $pharmacy_catalog_id_ref  = $dt_put['pharmacy_catalog_id_ref'];
        // $pharmacy_catalog_id_sub  = $dt_put['pharmacy_catalog_id_sub'];
        $doctor_in_charge_id  = $dt_put['doctor_in_charge_id'];
        $user  = $dt_put['user'];
        $used  = $dt_put['used'] ?? null;
        $notes  = $dt_put['notes'] ?? null;
        $administer_id  = $dt_put['administer_id'] ?? null;
        $dose  = $dt_put['dose'] ?? null;
        $dose_unit_id  = $dt_put['dose_unit_id'] ?? null;
        $signatura_times_id  = $dt_put['signatura_times_id'] ?? null;
        $consultation_via_type_id = $dt_put['consultation_via_type_id'];
        $consultation_via_anything_else = $dt_put['consultation_via_anything_else'];
        $advice = $dt_put['advice'];

        $select = 'a.pharmacy_prescription_id';
        $where = array(
            'a.pharmacy_prescription_id' => $prescription_id,
            'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
        );
        $prescription_item = $this->Prescription_item->select(0, $select, $where);
        if (count($prescription_item) == 0) {
            $this->REST_Return(404, 'there is no prescription_item with pharmacy_catalog_id ' . $pharmacy_catalog_id);
            return;
        }

        $select = 'a.id';
        $catalog = $this->Catalog->select($pharmacy_catalog_id, $select);
        if (count($catalog) == 0) {
            $this->REST_Return(404, 'there is no catalog with id ' . $pharmacy_catalog_id);
            return;
        }

        $select = 'a.id, a.package_id';
        $catalog_ref = $this->Catalog->select($pharmacy_catalog_id_ref, $select);
        if (count($catalog_ref) == 0) {
            $this->REST_Return(404, 'there is no catalog_ref with id ' . $pharmacy_catalog_id_ref);
            return;
        }

        $catalog_ref_package_id = $catalog_ref[0]->package_id;

        $select = 'a.id, u.id as user_id';
        $where = array();
        $join = array(
            array('user u', 'a.id = u.employee_id', 'INNER'),
        );
        $employee = $this->Employee->select($doctor_in_charge_id, $select, $where, $join);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $user_doctor_id = $employee[0]->user_id;

        if ($signatura_times_id != null) {
            $select = 'a.id';
            $signatura_times = $this->Signatura_times->select($signatura_times_id, $select);
            if (count($signatura_times) == 0) {
                $this->REST_Return(404, 'signatura times with id ' . $signatura_times_id . ' does not exist');
                return;
            }
        }

        if ($dose_unit_id != null) {
            $select = 'a.id';
            $dose_unit = $this->Unit->select($dose_unit_id, $select);
            if (count($dose_unit) == 0) {
                $this->REST_Return(404, 'unit with id ' . $dose_unit_id . ' does not exist');
                return;
            }
        }

        if ($administer_id != null) {
            $select = 'a.id';
            $administer = $this->Administer->select($administer_id, $select);
            if (count($administer) == 0) {
                $this->REST_Return(404, 'administer with id ' . $administer_id . ' does not exist');
                return;
            }
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_prescription_item = array(
            'pharmacy_catalog_id'     => $pharmacy_catalog_id_ref,
            'pharmacy_catalog_id_ref' => $pharmacy_catalog_id,
            'doctor_in_charge_id'     => $doctor_in_charge_id,
            'user'                    => $user_id,
            'used'                    => $used,
            'notes'                   => $notes,
            'dose'                    => $dose,
            'dose_unit_id'            => $dose_unit_id,
            'administer_id'           => $administer_id,
            'signatura_times_id'      => $signatura_times_id,
        );

        $where_delete = array(
            'pharmacy_prescription_id' => $prescription_id,
            'pharmacy_catalog_id' => $pharmacy_catalog_id,
        );
        $aff_row = $this->Prescription_item->update_v2(0, $dt_prescription_item, $where_delete);
        if ($aff_row > 1) {
            $this->REST_Return(422, 'something when wrong while updating prescription_item');
            return;
        }

        if ($registration_destination_id !== null && $registration_destination_id !== '') {
            $select = 'a.id';
            $where = array(
                'k.registration_destination_id' => $registration_destination_id,
                'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $where_kardex_item = "(k.is_active = 1 OR k.pharmacy_prescription_id = '$prescription_id')";
            $this->db->where($where_kardex_item);
            $join = array(
                array('kardex k', 'a.kardex_id = k.id', 'LEFT'),
            );
            $kardex_item = $this->Kardex_item->select(0, $select, $where, $join);
            for ($i=0; $i < count($kardex_item); $i++) { // Looping karena bisa dapet 2, satu yang memiliki no resep, satu lagi yang aktif
                $kardex_item_id = $kardex_item[$i]->id;
    
                $dt_kardex_item = array(
                    'pharmacy_catalog_id' => $pharmacy_catalog_id_ref,
                    'package_id'          => $catalog_ref_package_id,
                    'dose'                => $dose,
                    'dose_unit_id'        => $dose_unit_id,
                    'administer_id'       => $administer_id,
                    'signatura_times_id'  => $signatura_times_id,
                    'user_updated'        => $user_id,
                    'date_updated'        => date('Y-m-d H:i:s'),
                );
    
                $aff_row = $this->Kardex_item->update_v2($kardex_item_id, $dt_kardex_item);
                if ($aff_row > 1) {
                    $this->REST_Return(422, 'something when wrong while updating kardex_item');
                    return;
                }
            }
            
            if (count($kardex_item) > 0) {
                $select = 'a.id, a.doctor_in_charge_id';
                $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        
                $dpjp_id = $registration_destination[0]->doctor_in_charge_id;
                
                // SOAP
                $dt_soap = array(
                    'doctor_in_charge_id' => $dpjp_id,
                    'registration_destination_id' => $registration_destination_id,
                    'user_added' => $user_id,
                    'date_added' => date("Y-m-d H:i:s",time()),
                    'date_updated' => date("Y-m-d H:i:s",time()),
                );
                
                $select = 'a.id';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.is_delete' => 0,
                    'a.is_resume_exit' => 0,
                    'a.is_booking' => 0,
                    'a.is_initial_inpatient' => 0,
                );
                $soap = $this->Soap->select(0, $select, $where);
                if (count($soap) == 0) {
                    $soap_id = $this->Soap->create($dt_soap);
                } else {
                    $soap_id = $soap[0]->id;
                }
        
                // SBAR
                $dt_sbar = array(
                    'registration_destination_id'    => $registration_destination_id,
                    'doctor_in_charge_id'            => $user_employee_id,
                    'doctor_in_charge_id_sbar'       => $doctor_in_charge_id,
                    'soap_id'                        => $soap_id,
                    'consultation_via_type_id'       => $consultation_via_type_id,
                    'consultation_via_anything_else' => $consultation_via_anything_else,
                    'sbar'                           => $notes,
                    'advice'                         => $advice,
                    'validation_status'              => 1,
                    'validation_time'                => date('Y-m-d H:i:s', time()),
                    'user_added'                     => $user_id,
                    'date_added'                     => date("Y-m-d H:i:s",time()),
                    'date_updated'                   => date("Y-m-d H:i:s",time()),
                );
        
                $sbar_id = $this->Sbar->create($dt_sbar);
        
                $select = 'a.norm, a.name, h.name as alias';
                $where = array();
                $join = array(
                    array('helper h', 'a.alias = h.code AND h.variable="alias"', 'LEFT'),
                );
                $patient = $this->Patient->select($norm, $select, $where, $join);

                $patient_alias = $patient[0]->alias;
                $patient_name = $patient[0]->name;
        
                $title = 'SBAR';
                $message = "Substitusi obat Pasien $patient_alias $patient_name ($norm), Dikirim oleh $user_employee_name. Mohon verifikasi SBAR ini";
                $this->Notifications->createNotification($user_doctor_id, null, $title, $message, 'sbar', ['sbar_id' => $sbar_id]);
            }
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_prescription_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to change catalog', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    

    public function check_antibiotics_get($p = 0) {
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

        if (!$id){
            // daftar field yang dianggap penting (wajib ada salah satunya)
            $requiredFields = [
                'registration_id',
                'registration_destination_id',
                'soap_id',
                'norm',
                'doctor_in_charge_id',
            ];
    
            // cek apakah ada salah satu field yang tidak kosong
            $hasRequired = false;
            foreach ($requiredFields as $field) {
                if (!empty($q_strings[$field])) {
                    $hasRequired = true;
                    break;
                // } else {
                //     if ($id != null || $id != "" ){
                //         $hasRequired = true;
                //         break;
                //     }
                }
            }
    
            
            if (!$hasRequired) {
                $this->REST_Return(400, 'Minimal salah satu parameter wajib diisi.');
                return;
            }
        }

        $where = $q_strings;

        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {;
            $this->$model->limit = $q_strings['limit']; 
            $this->$model->offset = $q_strings['offset']; 
            unset($where['limit']);
            unset($where['offset']);
        }

        if(isset($q_strings['registration_destination_id'])){
            $where['a.registration_destination_id'] = $q_strings['registration_destination_id'];
            unset($where['registration_destination_id']);
        }

        if(isset($q_strings['norm'])){
            $where['a.norm'] = $q_strings['norm'];
            unset($where['norm']);
        }

        if(isset($q_strings['doctor_in_charge_id'])) {
            $where['a.doctor_in_charge_id'] = $q_strings['doctor_in_charge_id'];
            unset($where['doctor_in_charge_id']);
        }

        if(isset($q_strings['soap_id'])) {
            $where['sp.soap_id'] = $q_strings['soap_id'];
            unset($where['soap_id']);
        }
        

        if(isset($q_strings['registration_id'])){
            $registration_id = $q_strings['registration_id'];
            $output = $this->db->query("
                -- Case 1: a.registration_destination_id is NOT NULL
                SELECT  a.id,a.date_1, pi.pharmacy_catalog_id, pi.dose_doctor, pi.dose, pi.qty_doctor, pi.qty, pi.pharmacy_prescription_id, pi.sort, pi.package_id, pi.status_kardex, pi.pharmacy_prescription_id,
                pi.administer_id,pi.dose_unit_id,pi.qty_retur,pi.qty_copy,pi.is_retur,pi.is_retur_full,pi.doctor_in_charge_id,pi.pharmacy_catalog_id_ref,pi.pharmacy_catalog_id_retur,pi.pharmacy_catalog_id_sub, pi.signatura_times_id, 
                ee.fullname as doctor_in_charge_name,pc.price,pc.bride_code,
                pc.name as pharmacy_catalog_name,
                sub.name as pharmacy_catalog_name_sub,
                ref.name as pharmacy_catalog_name_ref,
                u.name as dose_unit_name,
                st.name as signatura_times_name,
                pci.name as administer_name,
                pp.name as package_name

                FROM pharmacy_prescription AS a
                LEFT JOIN pharmacy_depo d ON a.pharmacy_depo_id = d.id
                LEFT JOIN patient p ON a.norm = p.norm
                LEFT JOIN health_facility_item hfi ON a.health_facility_item_id = hfi.id
                LEFT JOIN soap_p sp ON a.soap_p_id = sp.id
                LEFT JOIN soap s ON sp.soap_id = s.id
                JOIN registration_destination rd ON a.registration_destination_id = rd.id
                JOIN registration_insurance ri ON a.registration_destination_id = ri.registration_destination_id AND ri.is_primary = '1'
                LEFT JOIN company c ON ri.company_id = c.id
                LEFT JOIN company cc ON ri.company_id_cob = cc.id
                LEFT JOIN helper hri ON ri.insurance_id = hri.code AND hri.variable='insurance'
                LEFT JOIN employee e ON a.doctor_in_charge_id = e.id
                LEFT JOIN employee dpjp ON s.doctor_in_charge_id = dpjp.id
                JOIN helper hf ON a.flag = hf.code AND hf.variable = 'type_prescription_flag'
                JOIN helper hf2 ON a.type_prescription_id = hf2.code AND hf2.variable = 'type_prescription'
                
                LEFT JOIN pharmacy_prescription_item pi ON a.id = pi.pharmacy_prescription_id
                LEFT JOIN employee ee ON pi.doctor_in_charge_id = ee.id
                LEFT JOIN pharmacy_catalog pc ON pi.pharmacy_catalog_id = pc.id
                LEFT JOIN pharmacy_catalog sub ON pi.pharmacy_catalog_id_sub = sub.id
                LEFT JOIN pharmacy_catalog ref ON pi.pharmacy_catalog_id_ref = ref.id
                LEFT JOIN package pp ON pi.package_id = pp.id
                LEFT JOIN unit u ON pi.dose_unit_id = u.id
                LEFT JOIN signatura_times st ON pi.signatura_times_id = st.id
                LEFT JOIN pharmacy_catalog_instruction pci ON pi.administer_id = pci.id

                WHERE rd.registration_id = '$registration_id'
                AND pc.is_antibiotics = 2
                AND a.is_delete = 0

                UNION ALL

                -- Case 2: a.registration_destination_id IS NULL, use soap.registration_destination_id
                SELECT  a.id,a.date_1,  pi.pharmacy_catalog_id, pi.dose_doctor, pi.dose, pi.qty_doctor, pi.qty, pi.pharmacy_prescription_id, pi.sort, pi.package_id, pi.status_kardex, pi.pharmacy_prescription_id,
                pi.administer_id,pi.dose_unit_id,pi.qty_retur,pi.qty_copy,pi.is_retur,pi.is_retur_full,pi.doctor_in_charge_id,pi.pharmacy_catalog_id_ref,pi.pharmacy_catalog_id_retur,pi.pharmacy_catalog_id_sub, pi.signatura_times_id, 
                ee.fullname as doctor_in_charge_name,pc.price,pc.bride_code,
                pc.name as pharmacy_catalog_name,
                sub.name as pharmacy_catalog_name_sub,
                ref.name as pharmacy_catalog_name_ref,
                u.name as dose_unit_name,
                st.name as signatura_times_name,
                pci.name as administer_name,
                pp.name as package_name
                
                FROM pharmacy_prescription AS a
                LEFT JOIN pharmacy_depo d ON a.pharmacy_depo_id = d.id
                LEFT JOIN patient p ON a.norm = p.norm
                LEFT JOIN health_facility_item hfi ON a.health_facility_item_id = hfi.id
                LEFT JOIN soap_p sp ON a.soap_p_id = sp.id
                LEFT JOIN soap s ON sp.soap_id = s.id
                JOIN registration_destination rd ON s.registration_destination_id = rd.id
                JOIN registration_insurance ri ON s.registration_destination_id = ri.registration_destination_id AND ri.is_primary = '1'
                LEFT JOIN company c ON ri.company_id = c.id
                LEFT JOIN company cc ON ri.company_id_cob = cc.id
                LEFT JOIN helper hri ON ri.insurance_id = hri.code AND hri.variable='insurance'
                LEFT JOIN employee e ON a.doctor_in_charge_id = e.id
                LEFT JOIN employee dpjp ON s.doctor_in_charge_id = dpjp.id
                JOIN helper hf ON a.flag = hf.code AND hf.variable = 'type_prescription_flag'
                JOIN helper hf2 ON a.type_prescription_id = hf2.code AND hf2.variable = 'type_prescription'
                
                
                LEFT JOIN pharmacy_prescription_item pi ON a.id = pi.pharmacy_prescription_id
                LEFT JOIN employee ee ON pi.doctor_in_charge_id = ee.id
                LEFT JOIN pharmacy_catalog pc ON pi.pharmacy_catalog_id = pc.id
                LEFT JOIN pharmacy_catalog sub ON pi.pharmacy_catalog_id_sub = sub.id
                LEFT JOIN pharmacy_catalog ref ON pi.pharmacy_catalog_id_ref = ref.id
                LEFT JOIN package pp ON pi.package_id = pp.id
                LEFT JOIN unit u ON pi.dose_unit_id = u.id
                LEFT JOIN signatura_times st ON pi.signatura_times_id = st.id
                LEFT JOIN pharmacy_catalog_instruction pci ON pi.administer_id = pci.id

                WHERE (a.registration_destination_id IS NULL OR a.registration_destination_id = '') 
                AND rd.registration_id = '$registration_id'
                AND pc.is_antibiotics = 2
                AND a.is_delete = 0

                ORDER BY date_1 DESC
            ")->result();
        } else {
            $output = $this->$model->select(0, $select, $where, $join, $order);
        }

        for ($i=0; $i < count($output); $i++) { 
            // $this->Prescription_item->whereFields['a.is_delete'] = 0;
            // $this->Prescription_item->whereFields['pc.is_antibiotics'] = 2;
            // $form_prescription = $this->Prescription_item->read($output[$i]->id);
            $form_prescription_parent = array();
            $pharmacy_prescription_id = $output[$i]->pharmacy_prescription_id;
            $pharmacy_catalog_id = $output[$i]->pharmacy_catalog_id;
            $package_id = $output[$i]->package_id;

            $kardex_status = null;
            $kardex_is_change = null;
            $kardex_dose_unit_id = null;
            $kardex_dose_unit_name = null;
            $kardex_signatura_times_id = null;
            $kardex_signatura_times_name = null;
            $kardex_administer_id = null;
            $kardex_administer_name = null;

            $select = 'a.id, ki.dose_unit_id, u.name as dose_unit_name, ki.signatura_times_id, st.name as signatura_times_name, ki.administer_id, pci.name as administer_name, ki.status, ki.is_change';
            $where = array(
                'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
                'ki.pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $join = array(
                array('kardex_item ki', 'a.id = ki.kardex_id', 'INNER'),
                array('unit u', 'ki.dose_unit_id = u.id', 'INNER'),
                array('signatura_times st', 'ki.signatura_times_id = st.id', 'INNER'),
                array('pharmacy_catalog_instruction pci', 'ki.administer_id = pci.id', 'INNER'),
            );
            $kardex = $this->Kardex->select(0, $select, $where, $join);
            if (count($kardex) > 0) {
                $kardex_status = $kardex[0]->status;
                $kardex_is_change = $kardex[0]->is_change;
                $kardex_dose_unit_id = $kardex[0]->dose_unit_id;
                $kardex_dose_unit_name = $kardex[0]->dose_unit_name;
                $kardex_signatura_times_id = $kardex[0]->signatura_times_id;
                $kardex_signatura_times_name = $kardex[0]->signatura_times_name;
                $kardex_administer_id = $kardex[0]->administer_id;
                $kardex_administer_name = $kardex[0]->administer_name;
            }

            $catalog = $this->Catalog->read($output[$i]->pharmacy_catalog_id,false)[0];
            $output[$i]->pharmacy_prescription_id = $output[$i]->pharmacy_prescription_id;
            $output[$i]->pharmacy_catalog_id = $output[$i]->pharmacy_catalog_id;
            $output[$i]->pharmacy_catalog_name = $catalog->name;
            $output[$i]->pharmacy_catalog_dose = $catalog->dose;
            $output[$i]->pharmacy_catalog_dose_unit_id = $catalog->dose_unit_id;
            $output[$i]->pharmacy_catalog_dose_unit_name = $catalog->dose_unit_name;
            $output[$i]->pharmacy_catalog_packaging_value = $catalog->packaging_value;
            $output[$i]->pharmacy_catalog_packaging_unit_id = $catalog->packaging_unit_id;
            $output[$i]->pharmacy_catalog_packaging_unit_name = $catalog->packaging_unit_name;
            $output[$i]->is_antibiotics = $catalog->is_antibiotics;
            $output[$i]->package_id = $output[$i]->package_id;
            $output[$i]->package_name = $output[$i]->package_name;
            $output[$i]->signatura_times_id = $output[$i]->signatura_times_id;
            $output[$i]->signatura_times_name = $output[$i]->signatura_times_name;
            $output[$i]->administer_id = $output[$i]->administer_id;
            $output[$i]->administer_name = $output[$i]->administer_name;
            $output[$i]->dose = $output[$i]->dose;
            $output[$i]->dose_unit_id = $output[$i]->dose_unit_id;
            $output[$i]->dose_unit_name = $output[$i]->dose_unit_name;
            $output[$i]->dose_name = $catalog->dose_name;
            $output[$i]->qty_doctor = $output[$i]->qty_doctor;
            $output[$i]->qty = $output[$i]->qty;
            $output[$i]->qty_retur = $output[$i]->qty_retur;
            $output[$i]->qty_copy = $output[$i]->qty_copy;
            $output[$i]->is_retur = $output[$i]->is_retur;
            $output[$i]->is_retur_full = $output[$i]->is_retur_full;
            $output[$i]->doctor_in_charge_id = $output[$i]->doctor_in_charge_id;
            $output[$i]->pharmacy_catalog_id_ref = $output[$i]->pharmacy_catalog_id_ref;
            $output[$i]->pharmacy_catalog_id_retur = $output[$i]->pharmacy_catalog_id_retur;
            $output[$i]->pharmacy_catalog_id_sub = $output[$i]->pharmacy_catalog_id_sub;
            $output[$i]->sort = $output[$i]->sort;
            $output[$i]->status_kardex = $output[$i]->status_kardex;
            $output[$i]->kardex_status = $kardex_status;
            $output[$i]->kardex_is_change = $kardex_is_change;
            $output[$i]->kardex_dose_unit_id = $kardex_dose_unit_id;
            $output[$i]->kardex_dose_unit_name = $kardex_dose_unit_name;
            $output[$i]->kardex_signatura_times_id = $kardex_signatura_times_id;
            $output[$i]->kardex_signatura_times_name = $kardex_signatura_times_name;
            $output[$i]->kardex_administer_id = $kardex_administer_id;
            $output[$i]->kardex_administer_name = $kardex_administer_name;

        }

        // $filtered = array_filter($output, function ($item) {
        //     return property_exists($item, 'form_prescription');
        // });
        
        // $filtered = array_values($filtered);

        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * P0-4: nilai tagihan resep dihitung server.
     * nilai = SUM( MAX(qty - qty_retur, 0) x var_1 ) atas item yang tidak dihapus.
     * qty dan qty_retur tetap tersimpan sebagai histori; retur penuh menghasilkan 0
     * tanpa menghapus baris. var_2 kiriman client tidak pernah dipakai di sini.
     */
    protected function _bill_total($prescription_id) {
        $row = $this->db->query(
            "SELECT IFNULL(SUM(GREATEST(IFNULL(qty,0) - IFNULL(qty_retur,0), 0) * IFNULL(var_1,0)),0) AS total
             FROM pharmacy_prescription_item WHERE pharmacy_prescription_id = ? AND is_delete = 0",
            array($prescription_id))->row();
        return round((float) $row->total, 2);
    }

    /**
     * 4C-02 R1 — validasi stok SELURUH item sebelum ada penulisan apa pun.
     *
     * - qty wajib numerik dan >= 0 (nilai negatif / non-numerik ditolak 422, bukan di-coerce).
     * - qty dijumlahkan per (depo, catalog) supaya dua baris obat yang sama tidak lolos
     *   masing-masing sendiri-sendiri.
     * - tiap baris pharmacy_assets dikunci FOR UPDATE, dalam urutan catalog_id yang
     *   deterministik, sehingga dua transaksi paralel tidak saling deadlock.
     *
     * @return array|null null = semua cukup; array = error (reason dipakai sebagai code).
     */
    protected function _c2_check_stock($dt_put, $prescription_item, $depo_id) {
        $need = array();
        for ($i = 0; $i < count($prescription_item); $i++) {
            if (!isset($dt_put['form_prescription'][$i])) {
                return array('reason' => 'INVALID_ITEM', 'index' => $i);
            }
            $row = $dt_put['form_prescription'][$i];
            $qty = isset($row['qty']) ? $row['qty'] : null;
            if (!is_numeric($qty) || (float) $qty < 0) {
                return array('reason' => 'INVALID_QTY', 'index' => $i, 'qty' => $qty);
            }
            $catalog_id = !empty($row['pharmacy_catalog_id_sub'])
                ? $row['pharmacy_catalog_id_sub']
                : (isset($row['pharmacy_catalog_id']) ? $row['pharmacy_catalog_id'] : '');
            if (!is_string($catalog_id) || trim($catalog_id) === '') {
                return array('reason' => 'INVALID_CATALOG', 'index' => $i);
            }
            $need[$catalog_id] = (isset($need[$catalog_id]) ? $need[$catalog_id] : 0) + (float) $qty;
        }

        ksort($need);
        foreach ($need as $catalog_id => $qty) {
            $q = $this->db->query(
                "SELECT admin_amount, physical_amount FROM pharmacy_assets
                  WHERE pharmacy_depo_id = ? AND pharmacy_catalog_id = ? FOR UPDATE",
                array($depo_id, $catalog_id));
            $row   = ($q === FALSE) ? null : $q->row();
            $admin = $row ? (float) $row->admin_amount : 0;
            $phys  = $row ? (float) $row->physical_amount : 0;
            $avail = min($admin, $phys);
            if ($qty > $avail) {
                return array(
                    'reason'              => 'STOCK_INSUFFICIENT',
                    'pharmacy_catalog_id' => $catalog_id,
                    'required'            => $qty,
                    'available'           => $avail,
                );
            }
        }
        return null;
    }

    /**
     * 4C-02 R1 — pengurangan stok atomik.
     *
     * Satu statement UPDATE dengan guard di WHERE (compare-and-set), bukan baca-lalu-tulis:
     * dua penulis paralel tidak bisa sama-sama lolos. Dipanggil setelah baris dikunci oleh
     * _c2_check_stock(), jadi hasilnya deterministik di dalam transaksi yang sama.
     *
     * @return array|false nilai stok setelah pengurangan, atau false bila stok tidak cukup.
     */
    protected function _c2_decrement_stock($depo_id, $catalog_id, $qty) {
        $qty = (float) $qty;

        if ($qty <= 0) {
            $q   = $this->db->query(
                "SELECT admin_amount, physical_amount FROM pharmacy_assets
                  WHERE pharmacy_depo_id = ? AND pharmacy_catalog_id = ?",
                array($depo_id, $catalog_id));
            $row = ($q === FALSE) ? null : $q->row();
            return array(
                'prev_amount'     => $row ? $row->admin_amount : 0,
                'admin_amount'    => $row ? $row->admin_amount : 0,
                'physical_amount' => $row ? $row->physical_amount : 0,
            );
        }

        $q = $this->db->query(
            "UPDATE pharmacy_assets
                SET prev_amount = admin_amount,
                    physical_amount = physical_amount - ?,
                    admin_amount = admin_amount - ?,
                    date_updated = ?
              WHERE pharmacy_depo_id = ? AND pharmacy_catalog_id = ?
                AND admin_amount >= ? AND physical_amount >= ?",
            array($qty, $qty, date('Y-m-d H:i:s'), $depo_id, $catalog_id, $qty, $qty));

        if ($q === FALSE || $this->db->affected_rows() !== 1) {
            return false;
        }

        $r   = $this->db->query(
            "SELECT prev_amount, admin_amount, physical_amount FROM pharmacy_assets
              WHERE pharmacy_depo_id = ? AND pharmacy_catalog_id = ?",
            array($depo_id, $catalog_id));
        $row = ($r === FALSE) ? null : $r->row();
        return array(
            'prev_amount'     => $row ? $row->prev_amount : 0,
            'admin_amount'    => $row ? $row->admin_amount : 0,
            'physical_amount' => $row ? $row->physical_amount : 0,
        );
    }

    /**
     * 4C-02 — audit penyerahan ke log_activity (tabel & kolom yang sudah ada).
     * Tidak pernah menulis token/password/payload pasien.
     */
    protected function _c2_audit($result, $prescription_id, $user_id, $detail = array(), $http = null) {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        $row = array(
            'id'            => vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4)),
            'noregistrasi'  => null,
            'user_id'       => $user_id,
            'user_name'     => isset($this->s1_actor['username']) ? $this->s1_actor['username'] : null,
            'category_id'   => 0,
            'description'   => json_encode(array_merge(array(
                'request_id'      => $this->debug_uniq,
                'endpoint'        => 'pharmacy/prescription',
                'action'          => strtolower(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'),
                'operation'       => 'transaction',
                'prescription_id' => $prescription_id,
                'branch'          => isset($this->s1_actor['branch']) ? $this->s1_actor['branch'] : null,
                'result'          => $result,
                'http_status'     => $http,
                // 4C-05: no_rawat (nomor kunjungan) ikut dicatat di setiap audit farmasi.
                'no_rawat'        => $this->_ph_no_rawat($prescription_id),
            ), $detail)),
            'related_id'    => $prescription_id,
            'related_table' => 'pharmacy_prescription',
            'created_at'    => date('Y-m-d H:i:s'),
        );
        $ok = $this->db->query(
            "INSERT INTO log_activity (id, noregistrasi, user_id, user_name, category_id, description, "
            . "related_id, related_table, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($row['id'], $row['noregistrasi'], $row['user_id'], $row['user_name'], $row['category_id'],
                  $row['description'], $row['related_id'], $row['related_table'], $row['created_at']));
        if (!$ok) {
            Logger::error($this->debug_uniq, 'C2 audit write failed: ' . $result);
        }
        return (bool) $ok;
    }

    /**
     * 4C-03C — identitas penjamin untuk dimensi harga, dibaca dari DB.
     * Sumber: registration_insurance (is_primary=1, tidak dibatalkan).
     */
    protected function _c3c_identity($registration_destination_id) {
        $q = $this->db->query(
            "SELECT insurance_id, service_class_id FROM registration_insurance
              WHERE registration_destination_id = ? AND is_primary = 1 AND (is_cancel = 0 OR is_cancel IS NULL)
              ORDER BY insurance_id ASC LIMIT 1",
            array($registration_destination_id)
        );
        $row = ($q === FALSE || $q->num_rows() === 0) ? null : $q->row();
        return array(
            'insurance_id'     => ($row === null) ? 0 : (int) $row->insurance_id,
            'service_class_id' => ($row === null || $row->service_class_id === null) ? '' : (string) $row->service_class_id,
        );
    }

    /**
     * ALKES GUARD — daftar item resep yang katalognya berkategori "Alat Kesehatan".
     * Kosong = aman. Dipakai create_post sebelum transaksi dimulai (fail-closed).
     */
    protected function _alkes_in_prescription($dt_post) {
        $ids = array();
        if (isset($dt_post['form_prescription']) && is_array($dt_post['form_prescription'])) {
            foreach ($dt_post['form_prescription'] as $row) {
                $cid = isset($row['pharmacy_catalog_id']) ? trim((string) $row['pharmacy_catalog_id']) : '';
                if ($cid !== '') { $ids[$cid] = true; }
            }
        }
        if (empty($ids)) { return array(); }

        $ids = array_keys($ids);
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->db->query(
            "SELECT c.id, c.code, c.name
               FROM pharmacy_catalog c
               JOIN category_catalog cc ON cc.id = c.category_catalog_id
              WHERE c.id IN ($ph) AND cc.name = 'Alat Kesehatan' AND IFNULL(cc.is_delete,0) = 0",
            $ids)->result();
        $out = array();
        foreach ($rows as $r) {
            $out[] = array('pharmacy_catalog_id' => $r->id, 'code' => $r->code, 'name' => $r->name);
        }
        return $out;
    }

    /**
     * 4C-03C — resolver harga canonical. Sumber tunggal: pharmacy_catalog_price.
     * Empat dimensi: branch, depo, service_class, insurance. Paling spesifik menang
     * (rank = branch*8 + depo*4 + class*2 + insurance*1). Tidak ada ORDER BY id DESC.
     * 0 kandidat  -> PRICE_NOT_FOUND
     * >1 pada rank tertinggi -> PRICE_AMBIGUOUS
     */
    protected function _c3c_resolve_price($catalog_id, $branch_id, $depo_id, $service_class_id, $insurance_id, $price_date) {
        $branch = trim((string) $branch_id);
        $depo   = trim((string) $depo_id);
        $class  = trim((string) $service_class_id);
        $ins    = (int) $insurance_id;

        $q = $this->db->query(
            "SELECT id, institute_branch_id, pharmacy_depo_id, service_class_id, insurance_id,
                    price_sale, price_purchase, price_hpp, valid_from,
                    (institute_branch_id = ?) AS m_branch,
                    (pharmacy_depo_id = ?)    AS m_depo,
                    (service_class_id = ?)    AS m_class,
                    (insurance_id = ?)        AS m_ins
               FROM pharmacy_catalog_price
              WHERE pharmacy_catalog_id = ?
                AND batch = ''
                AND is_active = 1
                AND valid_from <= ?
                AND (valid_to IS NULL OR valid_to >= ?)
                AND (institute_branch_id = ? OR institute_branch_id = '')
                AND (pharmacy_depo_id = ? OR pharmacy_depo_id = '')
                AND (service_class_id = ? OR service_class_id = '')
                AND (insurance_id = ? OR insurance_id = 0)",
            array($branch, $depo, $class, $ins, $catalog_id, $price_date, $price_date,
                  $branch, $depo, $class, $ins)
        );
        if ($q === FALSE) {
            return array('ok' => false, 'code' => 'PRICE_NOT_FOUND', 'reason' => 'query_error');
        }

        $rows = $q->result();
        if (count($rows) === 0) {
            return array('ok' => false, 'code' => 'PRICE_NOT_FOUND');
        }

        $best  = null;
        $ties  = 0;
        foreach ($rows as $r) {
            $rank = ((int) $r->m_branch) * 8 + ((int) $r->m_depo) * 4
                  + ((int) $r->m_class) * 2 + ((int) $r->m_ins);
            if ($best === null || $rank > $best['rank']) {
                $best = array('rank' => $rank, 'row' => $r);
                $ties = 1;
            } else if ($rank === $best['rank']) {
                $ties++;
            }
        }
        if ($ties > 1) {
            return array('ok' => false, 'code' => 'PRICE_AMBIGUOUS', 'candidates' => $ties);
        }
        return array('ok' => true, 'row' => $best['row']);
    }

    /**
     * 4C-03C — snapshot harga per item resep. var_1 legacy diisi price_sale server-side.
     */
    protected function _c3c_snapshot($price) {
        return array(
            'var_1'                  => $price->price_sale,
            'price_id'               => $price->id,
            'price_sale'             => $price->price_sale,
            'price_purchase'         => $price->price_purchase,
            'price_hpp'              => $price->price_hpp,
            'price_service_class_id' => (string) $price->service_class_id,
            'price_insurance_id'     => (int) $price->insurance_id,
            'price_branch_id'        => (string) $price->institute_branch_id,
            'price_valid_from'       => $price->valid_from,
            'price_snapshot_at'      => date('Y-m-d H:i:s'),
        );
    }

    /**
     * 4C-03D — guard snapshot harga pada penyerahan (dispensing).
     * Semua baris resep yang akan diserahkan wajib punya snapshot server-side
     * (price_sale + price_snapshot_at). Snapshot hilang/negatif => tolak, bukan ditebak.
     *
     * @return array|null null = semua baris punya snapshot valid.
     */
    protected function _c3d_snapshot_guard($prescription_item) {
        for ($i = 0; $i < count($prescription_item); $i++) {
            $row = $prescription_item[$i];
            $snapshot_at = isset($row->price_snapshot_at) ? (string) $row->price_snapshot_at : '';
            $price_sale  = isset($row->price_sale) ? $row->price_sale : null;
            $reason = null;
            if ($snapshot_at === '' || $snapshot_at === '0000-00-00 00:00:00') {
                $reason = 'snapshot_missing';
            } else if ($price_sale === null) {
                $reason = 'price_sale_null';
            } else if ((float) $price_sale < 0) {
                $reason = 'price_sale_negative';
            }
            if ($reason !== null) {
                return array(
                    'reason'              => $reason,
                    'index'               => $i,
                    'pharmacy_catalog_id' => isset($row->pharmacy_catalog_id) ? $row->pharmacy_catalog_id : null,
                    'price_id'            => isset($row->price_id) ? $row->price_id : null,
                    'old_price'           => isset($row->var_1) ? $row->var_1 : null,
                );
            }
        }
        return null;
    }

    /**
     * 4C-03D — harga satuan penyerahan = snapshot server-side (price_sale) pada baris resep.
     * Payload client tidak pernah dipakai. Bila harga baris berubah, catat ke $changes
     * (audit PRICE_APPLIED: old_price/new_price/snapshot_date/applied_by/applied_from_branch).
     */
    protected function _c3d_price_of($row, $user_id, $branch, &$changes) {
        $new = (float) $row->price_sale;
        $old = ($row->var_1 === null) ? null : (float) $row->var_1;
        if ($old === null || $old != $new) {
            $changes[] = array(
                'pharmacy_catalog_id' => $row->pharmacy_catalog_id,
                'price_id'            => $row->price_id,
                'old_price'           => $old,
                'new_price'           => $new,
                'snapshot_date'       => $row->price_snapshot_at,
                'applied_by'          => $user_id,
                'applied_from_branch' => $branch,
            );
        }
        return $new;
    }

    /**
     * 4C-03C-0 — audit penolakan update resep (immutability / branch scope).
     * Ditulis SETELAH rollback, tidak pernah sebelum commit. Tidak menulis token/password.
     */
    protected function _c30_audit($result, $prescription_id, $user_id, $username = null, $detail = array(), $http = null, $operation = 'update') {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        $audit_id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
        $description = json_encode(array_merge(array(
            'request_id'      => $this->debug_uniq,
            'endpoint'        => 'pharmacy/prescription',
            'action'          => ($operation === 'create' ? 'post' : 'put'),
            'operation'       => $operation,
            'prescription_id' => $prescription_id,
            'result'          => $result,
            'http_status'     => $http,
        ), $detail));
        $ok = $this->db->query(
            "INSERT INTO log_activity (id, noregistrasi, user_id, user_name, category_id, description, "
            . "related_id, related_table, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($audit_id, null, $user_id, $username, 0, $description,
                  $prescription_id, 'pharmacy_prescription', date('Y-m-d H:i:s')));
        if (!$ok) {
            Logger::error($this->debug_uniq, 'C3-0 audit write failed: ' . $result);
        }
        return (bool) $ok;
    }

    /** Tahap 1: jurnal otomatis; 'skipped' diaudit, 'error' => pemanggil rollback. */
    protected function post_journal($type, $ref_id, $ref_no, $branch, $user_id, $amounts, $note = '') {
        $this->load->library('Journal');
        $res = $this->journal->post($type, $ref_id, $ref_no, $branch, $user_id, $amounts, null, (string) $note);
        if (isset($res['status']) && $res['status'] !== 'posted' && $res['status'] !== 'duplicate') {
            $this->_c2_audit('JOURNAL_' . strtoupper($res['status']), $ref_id, $user_id, $res, 200);
        }
        return $res;
    }
}