<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * INAP BILLING — closing tagihan obat rawat inap.
 *
 * Mengisi gap Flow 3: resep inap bisa di-dispense (stok turun + jurnal HPP jalan) tetapi
 * belum ada jalur yang mengubahnya menjadi tagihan pasien. Endpoint ini mengumpulkan
 * SEMUA resep inap yang sudah diserahkan dan belum pernah ditagih, lalu membuat SATU
 * invoice berisi satu baris `transaction_invoice_item` type 14 per resep.
 *
 * Kenapa SATU invoice (bukan satu per dispensing): SOP closing rawat inap menagih sekali
 * saat pasien pulang. Auto-invoice per dispensing akan menghasilkan N invoice per
 * rawat inap dan menggandakan pekerjaan kasir.
 *
 * Kenapa tidak ada tabel/kolom baru:
 *   - `pharmacy_prescription` sudah menyimpan registration_destination_id + flag (0/1).
 *   - `pharmacy_prescription_item` sudah menyimpan snapshot harga (price_sale) + qty + qty_retur.
 *   - `transaction_invoice` + `transaction_invoice_item` (type 14) sudah dibaca oleh
 *     Piutang::_pj_invoice_lines dan Payment::_sync_piutang, jadi piutang/sisa tagihan
 *     langsung benar tanpa perubahan lain.
 *
 * Status "sudah ditagih" DITURUNKAN dari ada/tidaknya invoice_item type 14 yang menunjuk
 * resep tersebut — bukan kolom penanda baru, jadi tidak ada dua sumber kebenaran.
 *
 * Endpoint:
 *   POST v1/finance/inap_billing/create
 *        body: { registration_id, cashier_id? }
 *        -> { invoice_id, code, prescription_count, total, lines[] }
 *
 * Aturan yang ditegakkan:
 *   - branch hanya dari token (resolve_branch_id); registrasi beda branch => 404
 *   - registrasi harus rawat inap (type_register_id = 4) => 422
 *   - tidak ada resep yang bisa ditagih => 409 INAP_BILLING_EMPTY (bukan 200 kosong)
 *   - total <= 0 (harga snapshot hilang) => 422 INAP_BILLING_ZERO_PRICE, tidak menulis apa pun
 *   - idempoten: panggilan kedua => 409, invoice tidak dobel
 */
class Inap_billing extends My_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model("v1/tr/Transaction_invoice_model", "Invoice");
        $this->load->model("v1/tr/Transaction_invoice_item_model", "Invoice_item");
    }

    /* ===================== POST create ===================== */

    public function create_post() {
        $data = $this->json_post();
        $dt   = isset($data['dt_postput']) ? $data['dt_postput'] : array();

        $t = $this->authorization_token->validateToken();
        if (empty($t) OR $t['status'] !== TRUE) {
            $this->REST_Return(401, isset($t['message']) ? $t['message'] : 'Invalid Token.', array(), 'UNAUTHORIZED');
            return;
        }
        $user_id = $this->security->xss_clean($t['data']->id);
        $branch  = $this->resolve_branch_id($this->security->xss_clean($t['data']->institute_branch_id));
        if ($branch === '') {
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return;
        }

        $registration_id = isset($dt['registration_id']) ? trim((string) $dt['registration_id']) : '';
        if ($registration_id === '') {
            $this->REST_Return(422, 'registration_id wajib diisi.', array(), 'REGISTRATION_REQUIRED');
            return;
        }
        $cashier_id = isset($dt['cashier_id']) && trim((string) $dt['cashier_id']) !== ''
            ? $this->security->xss_clean($dt['cashier_id']) : $user_id;

        $reg = $this->db->query(
            "SELECT r.id, r.code, r.no_rawat, r.norm, r.type_register_id, r.institute_branch_id,
                    r.is_cancel
               FROM registration r
              WHERE r.id = ?",
            array($registration_id))->row();
        if ($reg === null
            || (string) $reg->institute_branch_id !== (string) $branch
            || (string) $reg->is_cancel === '1') {
            $this->REST_Return(404, 'Registrasi tidak ditemukan pada branch ini.', array(), 'REGISTRATION_NOT_FOUND');
            return;
        }
        if ((string) $reg->type_register_id !== '4') {
            $this->REST_Return(422, 'Endpoint ini hanya untuk registrasi rawat inap.',
                array('type_register_id' => $reg->type_register_id), 'NOT_INPATIENT');
            return;
        }

        // Resep inap yang sudah diserahkan dan belum pernah masuk invoice type 14.
        $rx = $this->db->query(
            "SELECT pp.id,
                    COALESCE(SUM((pi.qty - IFNULL(pi.qty_retur,0)) * IFNULL(pi.price_sale,0)), 0) AS amount,
                    COUNT(pi.pharmacy_catalog_id) AS item_count
               FROM pharmacy_prescription pp
               JOIN registration_destination rd ON rd.id = pp.registration_destination_id
               LEFT JOIN pharmacy_prescription_item pi
                      ON pi.pharmacy_prescription_id = pp.id AND IFNULL(pi.is_delete,0) = 0
              WHERE rd.registration_id = ?
                AND IFNULL(pp.is_delete,0) = 0
                AND IFNULL(pp.flag,0) = 1
                AND NOT EXISTS (
                    SELECT 1 FROM transaction_invoice_item tii
                      JOIN transaction_invoice ti
                        ON ti.id = tii.transaction_invoice_id AND IFNULL(ti.is_delete,0) = 0
                     WHERE tii.type_transaction_id = 14
                       AND tii.transaction_id = pp.id
                )
              GROUP BY pp.id
              ORDER BY pp.date_added ASC",
            array($registration_id))->result();

        if (empty($rx)) {
            $this->REST_Return(409, 'Tidak ada resep inap yang bisa ditagih.',
                array('registration_id' => $registration_id), 'INAP_BILLING_EMPTY');
            return;
        }

        $total = 0.0;
        $lines = array();
        foreach ($rx as $r) {
            $amt     = round((float) $r->amount, 2);
            $total  += $amt;
            $lines[] = array('pharmacy_prescription_id' => $r->id,
                             'item_count' => (int) $r->item_count,
                             'amount' => $amt);
        }
        $total = round($total, 2);

        if ($total <= 0) {
            $this->REST_Return(422, 'Total tagihan obat inap 0 — snapshot harga tidak lengkap.',
                array('lines' => $lines), 'INAP_BILLING_ZERO_PRICE');
            return;
        }

        // Nomor invoice: pakai counter yang sama dengan kasir (RBS), bukan format baru.
        // Counter bisa mengembalikan 0 pada bulan berjalan yang belum punya baris; pola
        // reset-nya disalin dari Transaction_invoice::create_post supaya nomor tidak
        // pernah keluar sebagai /00000/ (tidak unik).
        $this->load->library('transaction');
        $this->load->model('Counter_queue_model', 'Counter_queue');
        $cc = array('var_1' => 'RBS', 'var_2' => date('Y'), 'var_3' => date('m'), 'var_5' => date('Y-m'));
        $n  = $this->transaction->counter($cc);
        if ((string) $n['counter_queue'] === '0') {
            $this->Counter_queue->replace(array(
                'var_1' => 'RBS', 'var_2' => date('Y'), 'var_3' => date('m'),
                'var_5' => date('Y-m'), 'counter_queue' => 0,
            ));
            $n = $this->transaction->counter($cc);
        }
        $code = date('d') . '/' . $cc['var_3'] . '/' . $cc['var_1'] . '/'
              . str_pad($n['counter_queue'], 5, '0', STR_PAD_LEFT) . '/' . $cc['var_2'];

        $this->db->trans_start();
        $invoice_id = $this->Invoice->create(array(
            'institute_branch_id' => $branch,
            'registration_id'     => $registration_id,
            'code'                => $code,
            'cashier_id'          => $cashier_id,
            'is_delete'           => 0,
            'var_1'               => 'INAP',
            'var_2'               => '',
            'var_3'               => '',
            'date_added'          => date('Y-m-d H:i:s'),
        ));

        if ($invoice_id) {
            foreach ($rx as $r) {
                $this->Invoice_item->replace(array(
                    'transaction_invoice_id' => $invoice_id,
                    'transaction_id'         => $r->id,
                    'flag_status_id'         => 2,
                    'type_transaction_id'    => 14,
                ));
            }
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() === false || !$invoice_id) {
            $this->REST_Return(500, 'Gagal membuat invoice obat inap.',
                array('error' => $this->db->error()), 'INAP_BILLING_FAILED');
            return;
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'invoice_id'          => $invoice_id,
            'code'                => $code,
            'registration_id'     => $registration_id,
            'no_rawat'            => $reg->no_rawat,
            'prescription_count'  => count($rx),
            'total'               => $total,
            'lines'               => $lines,
        ));
    }
}
