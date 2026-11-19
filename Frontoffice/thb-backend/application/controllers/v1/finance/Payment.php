<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Payment extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/{$this->name}_method_model", "{$this->name}_method");
        $this->load->model("$this->directory/Cashier_model", "Cashier");
        $this->load->model("v1/tr/Accurate_bak_model", "Accurate_bak");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Insurance_model", "Insurance");
        $this->load->model("v1/tr/Transaction_invoice_model", "Transaction_invoice");

        $this->update_rules = array(
            array('field' => 'flag_status_id', 'label' => 'flag_status_id', 'rules' => 'trim|max_length[1]'),
        );
    }

    /**
     * Payment API
     * --------------------
     * @param: invoice_id
     * @param: cashier_id
     * @param: flag_status_id
     * @param: payment_items
     * --------------------
     * @method : POST
     * @link: version/finance/Payment
     **/

     public function create_post() {
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
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            // Branch dari token (sebelumnya variabel ini tidak pernah didefinisikan di
            // create_post, sehingga penulisan Accurate_bak memakai nilai kosong).
            // TODO: expand branch filter saat multi-cabang
            $institute_branch_id = $this->resolve_branch_id(
                $this->security->xss_clean($is_valid_token['data']->institute_branch_id));
            # Form Validation
            $this->form_validation->set_rules('invoice_id', 'invoice_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('cashier_id', 'cashier_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('flag_status_id', 'flag_status_id', 'trim|max_length[1]');
            // Data Payment_item
            $this->form_validation->set_rules('payment_items[]', 'payment_items', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Check invoice_id and cashier_id
                if ($dt_post['registration_id']){
                    $data_patient = $this->Registration->read($dt_post['registration_id'], false)[0];
                }
                if($this->Transaction_invoice->read($dt_post['invoice_id'], false) != []) {
                    $t = time();
                    $dt_payment = array(
                        'invoice_id'       => $dt_post['invoice_id'],
                        'cashier_id'       => $user_id,
                        'flag_status_id'   => isset($dt_post['flag_status_id']) ? $dt_post['flag_status_id'] : '',
                        'date_added'            => date("Y-m-d H:i:s",$t)
                    );
                    $id = $this->$model->create($dt_payment);
                    if ($id) {
                        $dt_invoice = array(
                            'id'               => $dt_post['invoice_id'],
                            'payment_id'       => $id,
                        );
                        $this->Transaction_invoice->replace($dt_invoice);
                    }
                    // $id = 1;
    
                    // INIT_PAYMENT_ITEM
                    for ($i=0; $i < count($dt_post['payment_items']); $i++) { 
                        if ($this->Payment_method->read($dt_post['payment_items'][$i]['payment_method_id'], false) != []) {
                            if ($dt_post['payment_items'][$i]['payment_method_id'] == '' || $dt_post['payment_items'][$i]['payment_method_id'] == null || $dt_post['payment_items'][$i]['payment_method_type_id'] == '' || $dt_post['payment_items'][$i]['payment_method_type_id'] == null) {
                                $this->REST_Return(422, 'Cara Bayar Harus diisi!' ,array());
                                return;
                            }
                            $dt_payment_item = array(
                                'payment_id'             => $id,
                                'payment_method_id'      => $dt_post['payment_items'][$i]['payment_method_id'],
                                'payment_method_type_id' => $dt_post['payment_items'][$i]['payment_method_type_id'],
                                'insurance_id'           => $dt_post['payment_items'][$i]['insurance_id'],
                                'var_1'                  => isset($dt_post['payment_items'][$i]['var_1']) ? $dt_post['payment_items'][$i]['var_1'] : '',
                                'var_2'                  => isset($dt_post['payment_items'][$i]['var_2']) ? $dt_post['payment_items'][$i]['var_2'] : '',
                                'var_3'                  => isset($dt_post['payment_items'][$i]['var_3']) ? $dt_post['payment_items'][$i]['var_3'] : ''
                            );
                            $this->Payment_item->replace($dt_payment_item);
                            $dt_pm = $this->Payment_method->read($dt_post['payment_items'][$i]['payment_method_id'], false)[0];
                            $detailItem[0] = array(
                                "invoiceNo"=> $this->Transaction_invoice->read($dt_post['invoice_id'], false)[0]->code,
                                "paymentAmount"=> $dt_post['amount_payment']
                            );
                            if ($dt_post['registration_id']) {
                                $json_body_payment = array(
                                    "bankNo"=> $dt_pm->coa,
                                    "chequeAmount" => isset($dt_post['payment_items'][$i]['var_3']) ? $dt_post['payment_items'][$i]['var_3'] : '',
                                    "detailInvoice" => $detailItem,
                                    "customerNo" => $data_patient->norm,
                                    "transDate" => date('d/m/Y')
                                );
                                // echo "<pre>";
                                // var_dump($json_body_payment);die();
                                $return_bride_payment = $this->bride_accurate('api/sales-receipt/save.do', json_encode($json_body_payment));
                                if ($return_bride_payment['s'] == true) {
                                    // var_dump($return_bride_payment);
                                    // $this->REST_Return(404, 'Failed Accurate = ');
                                    // return;
                                } else {
                                    $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$dt_post['invoice_id'],'payment_id'=>$id,'response'=>json_encode($return_bride_inv),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
                                    // var_dump($return_bride_payment);
                                    // $this->REST_Return(404, 'Failed Accurate = ');
                                    // return;
                                }
                            }

                        } else {
                            $this->REST_Return(404, 'There is no payment_method with id ' . $dt_post['payment_items'][$i]['payment_method_id']);
                            return;
                        }
                    }
                } else {
                    $this->REST_Return(404, 'Wrong invoice_id');
                    return;
                }

                // E-7: jurnal kasir. Debit kas/bank/EDC sesuai cara bayar, kredit piutang
                // pasien (pendapatan sudah diakui saat invoice dibuat -> tidak dobel).
                $pay_total = 0.0;
                if (isset($dt_post['payment_items']) && is_array($dt_post['payment_items'])) {
                    foreach ($dt_post['payment_items'] as $pi) {
                        $pay_total += (float) (isset($pi['var_3']) ? $pi['var_3'] : 0);
                    }
                }
                if ($pay_total <= 0 && isset($dt_post['amount_payment'])) {
                    $pay_total = (float) $dt_post['amount_payment'];
                }
                $first_item = (isset($dt_post['payment_items'][0]) && is_array($dt_post['payment_items'][0]))
                    ? $dt_post['payment_items'][0] : array();
                $debit_account = $this->_payment_account(
                    isset($first_item['payment_method_id']) ? $first_item['payment_method_id'] : null,
                    isset($first_item['payment_method_type_id']) ? $first_item['payment_method_type_id'] : null);
                $inv_row = $this->Transaction_invoice->read($dt_post['invoice_id'], false);
                $invoice_code = $inv_row ? $inv_row[0]->code : (string) $dt_post['invoice_id'];
                $journal = $this->post_journal('CASHIER_PAYMENT', $id, 'PAY-' . $id, $institute_branch_id,
                    $user_id,
                    array('cash' => round($pay_total, 2), 'receivable' => round($pay_total, 2)),
                    'invoice=' . $invoice_code, array('cash' => $debit_account));
                if (isset($journal['status']) && $journal['status'] === 'error') {
                    $this->db->trans_rollback();
                    $this->REST_Return(500, 'Jurnal pembayaran gagal (tidak balance).', $journal, 'JOURNAL_FAILED');
                    return;
                }

                // Kasir -> piutang. Sebelumnya pembayaran hanya menulis `payment` + jurnal,
                // sehingga sisa tagihan pasien tidak tercatat di mana pun. Sinkron ini
                // membuat/menyesuaikan baris `piutang` (sisa_piutang, status) dan mencatat
                // piutang_payment idempoten (CASH-<payment_id>). Gagal => rollback, karena
                // uang masuk tanpa sisa tagihan = data hilang.
                $pj_sync = $this->_sync_piutang($dt_post['invoice_id'], $id, $institute_branch_id,
                    $user_id, $pay_total,
                    isset($first_item['payment_method_id']) ? $first_item['payment_method_id'] : null);
                if (isset($pj_sync['status']) && $pj_sync['status'] === 'error') {
                    $this->db->trans_rollback();
                    $this->REST_Return(500, 'Sinkronisasi piutang gagal.', $pj_sync, 'PIUTANG_SYNC_FAILED');
                    return;
                }

                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = array(
                        'id'     => $id,
                        'journal' => isset($journal) ? $journal : null,
                        'piutang' => isset($pj_sync) ? $pj_sync : null,
                    );
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

    public function detail_get($p = 0){
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
            $this->$model->whereFields = $q_strings;
            $output = $this->$model->read($id);
            $response_data = array();
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
            $this->$model->whereLike = $q_strings;
            $output = $this->$model->read_ssr($id);
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

    public function delete_put($p = 0) {
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
            $this->form_validation->set_rules('reason', 'reason', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
                if($this->Transaction_invoice->read($id,false) != []) {
                    $dtinv = $this->Transaction_invoice->read($id,false)[0];
                    $dt_inv = array(
                        'id'                   => $id,
                        'payment_id'         => null
                    );
                    $this->Transaction_invoice->replace($dt_inv);
                    $dt_payment = array(
                        'id'                   => $dtinv->payment_id,
                        'invoice_id'           => $id,
                        'user_deleted'         => $user_id,
                        'date_deleted'         => date('Y-m-d H:i:s'),
                        'is_delete'            => 1,
                        'reason'               => $dt_put['reason']
                    );
                    $this->$model->replace($dt_payment);
                } else {
                    $this->REST_Return(404, 'Registration with id ' . $id . ' does not exist!');
                    return;
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

    /* ===================== E-7 helpers ===================== */

    /**
     * Akun debit pembayaran: pakai payment_method.coa bila akunnya ada di b_coa,
     * kalau tidak pakai peta tipe dari config/coa_mapping.php.
     */
    protected function _payment_account($method_id = null, $type_id = null) {
        $this->config->load('coa_mapping', TRUE);
        $map = $this->config->item('payment_method_accounts', 'coa_mapping');
        $map = is_array($map) ? $map : array();

        if ($method_id !== null && trim((string) $method_id) !== '') {
            $m = $this->db->query("SELECT coa, type_id FROM payment_method WHERE id = ?",
                array($method_id))->row();
            if ($m !== null) {
                $coa = trim((string) $m->coa);
                if ($coa !== '') {
                    $ok = $this->db->query(
                        "SELECT account FROM b_coa WHERE account = ? AND IFNULL(is_delete,0) = 0 LIMIT 1",
                        array($coa))->row();
                    if ($ok !== null) { return $coa; }
                }
                if ($type_id === null || trim((string) $type_id) === '') {
                    $type_id = $m->type_id;
                }
            }
        }
        $t = trim((string) $type_id);
        if ($t !== '' && isset($map[$t])) { return $map[$t]; }
        return isset($map['default']) ? $map['default'] : '1-1100';
    }

    /**
     * Kasir -> piutang (E-2 lanjutan).
     *
     * Pembayaran kasir sebelumnya hanya menulis `payment` + jurnal CASHIER_PAYMENT.
     * Tidak ada baris `piutang`, jadi sisa tagihan pasien tidak bisa dilacak dan
     * status penagihan tidak pernah berubah. Metode ini menyambungkannya dengan
     * pola yang SAMA seperti Piutang::pay_post supaya kedua jalur tidak berbeda:
     *
     *   - piutang per no_rawat; dibuat bila belum ada, total = SUM(amount) baris invoice
     *     (join identik dengan Piutang::_pj_invoice_lines).
     *   - piutang_payment idempoten: idempotency_key = 'CASH-' . payment_id, jadi
     *     replay/retry tidak menghitung dua kali.
     *   - total_dibayar/sisa_piutang/status di-update inkremental dengan guard
     *     `WHERE sisa_piutang = <nilai lama>` (deteksi balapan, sama seperti pay_post).
     *
     * Return array('status' => ok|skipped|error, ...). 'skipped' = bukan kondisi
     * fatal (mis. invoice tanpa baris nominal); 'error' = wajib rollback.
     */
    protected function _sync_piutang($invoice_id, $payment_id, $branch, $user_id, $amount, $method_id = null) {
        if (trim((string) $branch) === '' || (float) $amount <= 0) {
            return array('status' => 'skipped', 'reason' => 'BRANCH_OR_AMOUNT_EMPTY');
        }
        $inv = $this->db->query(
            "SELECT id, code, registration_id FROM transaction_invoice WHERE id = ?",
            array($invoice_id))->row();
        if ($inv === null) {
            return array('status' => 'skipped', 'reason' => 'INVOICE_NOT_FOUND');
        }

        $reg = $this->db->query(
            "SELECT r.id, r.no_rawat, r.norm, r.institute_branch_id,
                    rd.id AS registration_destination_id,
                    COALESCE(NULLIF(TRIM(p.name),''), p.fullname) AS patient_name
               FROM registration r
               LEFT JOIN registration_destination rd ON rd.registration_id = r.id
               LEFT JOIN patient p ON p.norm = r.norm
              WHERE r.id = ?
              ORDER BY rd.id LIMIT 1",
            array($inv->registration_id))->row();
        if ($reg === null || trim((string) $reg->no_rawat) === '') {
            return array('status' => 'skipped', 'reason' => 'REGISTRATION_INVALID');
        }

        $pj = $this->db->query(
            "SELECT * FROM piutang WHERE no_rawat = ? AND IFNULL(is_delete,0) = 0 FOR UPDATE",
            array($reg->no_rawat))->row();

        if ($pj === null) {
            $rows = $this->db->query(
                "SELECT COALESCE(li.var_1, ri.var_1, ta.var_1, td.var_1, tb.var_3,
                                 (pi.qty - IFNULL(pi.qty_retur,0)) * IFNULL(pi.price_sale,0), 0) AS amount
                   FROM transaction_invoice_item tii
                   -- Salinan ekspresi Piutang::_pj_invoice_lines; join lewat id saja
                   -- (tahan dua konvensi type id) + kamar inap transaction_bed.var_3.
                   LEFT JOIN laboratory_request_item li ON li.id = tii.transaction_id
                   LEFT JOIN radiology_request_item  ri ON ri.id = tii.transaction_id
                   LEFT JOIN transaction_administration ta ON ta.id = tii.transaction_id
                   LEFT JOIN transaction_doctor       td ON td.id = tii.transaction_id
                   LEFT JOIN transaction_bed          tb ON tb.id = tii.transaction_id
                   -- E-3: obat (type 14) = qty bersih x snapshot price_sale. Salinan query
                   -- Piutang::_pj_invoice_lines; keduanya harus selalu sama.
                   LEFT JOIN pharmacy_prescription_item pi ON pi.pharmacy_prescription_id = tii.transaction_id
                        AND tii.type_transaction_id = 14 AND IFNULL(pi.is_delete,0) = 0
                  WHERE tii.transaction_invoice_id = ? AND IFNULL(tii.is_delete,0) = 0",
                array($inv->id))->result();
            $total = 0.0;
            foreach ($rows as $r) { $total += (float) $r->amount; }
            $total = round($total, 2);
            if ($total <= 0) {
                return array('status' => 'skipped', 'reason' => 'INVOICE_LINES_EMPTY');
            }

            $this->load->library('transaction');
            $c   = $this->transaction->counter(array(
                'var_1' => 'PJ', 'var_2' => date('Y'), 'var_3' => date('m'), 'var_5' => date('Y-m'),
            ));
            $seq        = isset($c['counter_queue']) ? (int) $c['counter_queue'] : 1;
            $invoice_no = date('d') . '/' . date('m') . '/PJ/' . str_pad($seq, 5, '0', STR_PAD_LEFT) . '/' . date('Y');
            $pj_id      = $this->_uuid4();
            $now        = date('Y-m-d H:i:s');

            $this->db->query(
                "INSERT INTO piutang (id, invoice_no, institute_branch_id, registration_id,
                    registration_destination_id, no_rawat, norm, patient_name, insurance_id, company_id,
                    tgl_piutang, tgl_tempo, total_piutang, uang_muka, diskon, total_dibayar,
                    sisa_piutang, status, invoice_status, reason, created_by, created_at, is_delete)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0)",
                array($pj_id, $invoice_no, $branch, $reg->id, $reg->registration_destination_id,
                      $reg->no_rawat, $reg->norm, $reg->patient_name, 0, null,
                      date('Y-m-d'), null, $total, 0, 0, 0, $total,
                      'open', 'open', 'auto: kasir', $user_id, $now));
            $pj = $this->db->query("SELECT * FROM piutang WHERE id = ?", array($pj_id))->row();
            if ($pj === null) {
                return array('status' => 'error', 'reason' => 'PIUTANG_CREATE_FAILED');
            }
        }

        $idem = 'CASH-' . $payment_id;
        if (strlen($idem) > 64) { $idem = substr($idem, 0, 64); }
        $prev = $this->db->query(
            "SELECT id FROM piutang_payment WHERE piutang_id = ? AND idempotency_key = ?",
            array($pj->id, $idem))->row();
        if ($prev !== null) {
            return array('status' => 'skipped', 'reason' => 'REPLAY', 'piutang_id' => $pj->id,
                         'sisa_piutang' => (float) $pj->sisa_piutang, 'status_piutang' => $pj->status);
        }
        if ($pj->status === 'cancelled') {
            return array('status' => 'error', 'reason' => 'PIUTANG_CANCELLED', 'piutang_id' => $pj->id);
        }

        $sisa_before  = (float) $pj->sisa_piutang;
        $pay          = round((float) $amount, 2);
        $sisa_after   = round($sisa_before - $pay, 2);
        if ($sisa_after < 0) { $sisa_after = 0.0; }
        $status_after = ($sisa_after <= 0.001) ? 'paid' : 'partial';
        $now          = date('Y-m-d H:i:s');
        $pay_id       = $this->_uuid4();

        $this->db->query(
            "INSERT INTO piutang_payment (id, piutang_id, institute_branch_id, amount, discount,
                payment_method_id, cara_bayar, note, paid_at, paid_by, idempotency_key, is_void)
             VALUES (?,?,?,?,0,?,?,?,?,?,?,0)",
            array($pay_id, $pj->id, $branch, $pay, $method_id,
                  'KASIR', 'kasir payment ' . $payment_id, $now, $user_id, $idem));

        $this->db->query(
            "UPDATE piutang
                SET total_dibayar  = ROUND(total_dibayar + ?, 2),
                    sisa_piutang   = ?,
                    status         = ?,
                    invoice_status = ?,
                    updated_by     = ?,
                    updated_at     = ?
              WHERE id = ? AND sisa_piutang = ?",
            array($pay, $sisa_after, $status_after, $status_after, $user_id, $now,
                  $pj->id, $sisa_before));
        if ($this->db->affected_rows() !== 1) {
            return array('status' => 'error', 'reason' => 'PIUTANG_CONCURRENT_UPDATE', 'piutang_id' => $pj->id);
        }

        return array('status' => 'ok', 'piutang_id' => $pj->id, 'invoice_no' => $pj->invoice_no,
                     'payment_id' => $pay_id, 'amount' => $pay,
                     'sisa_piutang' => $sisa_after, 'status_piutang' => $status_after);
    }

    protected function _uuid4() {
        $d = random_bytes(16);
        $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
        $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
    }

    protected function post_journal($type, $ref_id, $ref_no, $branch, $user_id, $amounts, $note = '',
                                    $override = array()) {
        if (trim((string) $branch) === '') {
            return array('status' => 'skipped', 'reason' => 'BRANCH_UNDEFINED', 'type' => $type);
        }
        $this->load->library('Journal');
        return $this->journal->post($type, $ref_id, $ref_no, $branch, $user_id, $amounts, null,
            (string) $note, $override);
    }
}
