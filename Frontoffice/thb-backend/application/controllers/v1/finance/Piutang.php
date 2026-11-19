<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;

/**
 * PIUTANG PASIEN — modul tagihan piutang (isolated).
 *
 * Endpoint:
 *   GET  v1/finance/piutang/list     daftar piutang (filter branch wajib)
 *   POST v1/finance/piutang/create   buat piutang dari no_rawat
 *   POST v1/finance/piutang/pay      pembayaran (partial/full), idempoten
 *   POST v1/finance/piutang/return   batalkan piutang (retur)
 *   GET  v1/finance/piutang/report   rekap per periode
 *
 * Aturan yang ditegakkan server-side:
 *   - branch HANYA dari token; kosong/ tak cocok => ditolak (fail-closed)
 *   - no_rawat harus ada dan se-branch dengan token
 *   - total_piutang > 0; uang_muka + diskon <= total_piutang
 *   - pembayaran tidak boleh melebihi sisa; idempoten per idempotency_key
 *   - status berubah otomatis: open -> partial -> paid; cancelled final
 *   - setiap aksi & penolakan diaudit (log_activity, related_table='piutang')
 */
class Piutang extends My_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_payment_model", "{$this->name}_payment");
    }

    /* ===================== GET list ===================== */
    public function list_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_pj_branch('list');
        if ($branch === null) { return; }

        $where  = "p.institute_branch_id = ? AND p.is_delete = 0";
        $params = array($branch);
        if (isset($q['status']) && $q['status'] !== '') {
            $where .= " AND p.status = ?";
            $params[] = $q['status'];
        }
        if (isset($q['norm']) && $q['norm'] !== '') {
            $where .= " AND p.norm = ?";
            $params[] = $q['norm'];
        }
        if (isset($q['id']) && $q['id'] !== '') {
            $where .= " AND p.id = ?";
            $params[] = $q['id'];
        }
        if (isset($q['no_rawat']) && $q['no_rawat'] !== '') {
            $where .= " AND p.no_rawat = ?";
            $params[] = $q['no_rawat'];
        }
        if (isset($q['date_start']) && $q['date_start'] !== '') {
            $where .= " AND p.tgl_piutang >= ?";
            $params[] = $q['date_start'];
        }
        if (isset($q['date_end']) && $q['date_end'] !== '') {
            $where .= " AND p.tgl_piutang <= ?";
            $params[] = $q['date_end'];
        }
        $limit  = isset($q['limit']) ? max(1, min(200, (int) $q['limit'])) : 50;
        $offset = isset($q['offset']) ? max(0, (int) $q['offset']) : 0;

        $rows = $this->db->query(
            "SELECT p.*, c.name AS company_name
               FROM piutang p
               LEFT JOIN company c ON c.id = p.company_id
              WHERE $where
              ORDER BY p.tgl_piutang DESC, p.no_rawat DESC
              LIMIT $limit OFFSET $offset",
            $params)->result();
        $total = $this->db->query("SELECT COUNT(*) AS n FROM piutang p WHERE $where", $params)->row();

        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'id'            => $r->id,
                'invoice_no'    => $r->invoice_no,
                'invoice_status' => $r->invoice_status,
                'no_rawat'      => $r->no_rawat,
                'norm'          => $r->norm,
                'patient_name'  => $r->patient_name,
                'insurance_id'  => (int) $r->insurance_id,
                'company_name'  => $r->company_name,
                'tgl_piutang'   => $r->tgl_piutang,
                'tgl_tempo'     => $r->tgl_tempo,
                'total_piutang' => (float) $r->total_piutang,
                'uang_muka'     => (float) $r->uang_muka,
                'diskon'        => (float) $r->diskon,
                'total_dibayar' => (float) $r->total_dibayar,
                'sisa_piutang'  => (float) $r->sisa_piutang,
                'status'        => $r->status,
                'reason'        => $r->reason,
            );
        }
        $this->REST_Return(200, 'SUCCESS', (object) array(
            'rows' => $out, 'iTotal' => (int) $total->n, 'start' => $offset + $limit,
        ));
        return;
    }

    /* ===================== POST create ===================== */
    public function create_post($p = 0) {
        $data    = $this->json_post();
        $dt_post = $data['dt_postput'];

        $branch = $this->_pj_branch('create');
        if ($branch === null) { return; }
        $user_id = $this->_pj_user();

        $no_rawat = isset($dt_post['no_rawat']) ? trim((string) $dt_post['no_rawat']) : '';
        if ($no_rawat === '') {
            $this->REST_Return(422, 'no_rawat wajib diisi.', array(), 'NO_RAWAT_REQUIRED');
            return;
        }
        // E-2 (owner): total_piutang TIDAK diterima dari client. Dihitung server-side
        // dari baris invoice (transaction_invoice_item). Payload total_piutang diabaikan.
        $uang_muka = isset($dt_post['uang_muka']) ? (float) $dt_post['uang_muka'] : 0;
        $diskon   = isset($dt_post['diskon']) ? (float) $dt_post['diskon'] : 0;
        if ($uang_muka < 0 || $diskon < 0) {
            $this->REST_Return(422, 'uang_muka/diskon tidak boleh negatif.', array(), 'PIUTANG_AMOUNT_INVALID');
            return;
        }

        $this->db->trans_start();
        // no_rawat harus valid dan se-branch (fail-closed bila branch kunjungan kosong).
        $reg = $this->db->query(
            "SELECT r.id, r.no_rawat, r.norm, r.institute_branch_id,
                    rd.id AS registration_destination_id, COALESCE(NULLIF(TRIM(p.name),''), p.fullname) AS patient_name
               FROM registration r
               LEFT JOIN registration_destination rd ON rd.registration_id = r.id
               LEFT JOIN patient p ON p.norm = r.norm
              WHERE r.no_rawat = ?
              ORDER BY rd.id LIMIT 1",
            array($no_rawat))->row();
        if ($reg === null) {
            $this->_pj_deny('NO_RAWAT_NOT_FOUND', null, $user_id, 'create',
                array('no_rawat' => $no_rawat), 404, 'no_rawat tidak ditemukan.');
            return;
        }
        if (trim((string) $reg->institute_branch_id) === '') {
            $this->_pj_deny('NO_RAWAT_BRANCH_UNDEFINED', null, $user_id, 'create',
                array('no_rawat' => $no_rawat, 'registration_branch' => null), 409,
                'Kunjungan tidak memiliki branch.');
            return;
        }
        if ((string) $reg->institute_branch_id !== $branch) {
            $this->_pj_deny('NO_RAWAT_BRANCH_SCOPE_MISMATCH', null, $user_id, 'create',
                array('no_rawat' => $no_rawat, 'registration_branch' => $reg->institute_branch_id,
                      'token_branch' => $branch), 403, 'Kunjungan berada di luar branch scope.');
            return;
        }
        $exists = $this->db->query("SELECT id FROM piutang WHERE no_rawat = ?", array($no_rawat))->row();
        if ($exists !== null) {
            $this->_pj_deny('PIUTANG_ALREADY_EXISTS', $exists->id, $user_id, 'create',
                array('no_rawat' => $no_rawat, 'existing_id' => $exists->id), 409,
                'Piutang untuk no_rawat ini sudah ada.');
            return;
        }

        // Auto-calc: total = SUM(amount) baris invoice. Tanpa baris => tolak.
        $lines = $this->_pj_invoice_lines($reg->id);
        $total = 0.0;
        foreach ($lines as $l) { $total += (float) $l['amount']; }
        $total = round($total, 2);
        if (!$lines || $total <= 0) {
            $this->db->trans_rollback();
            $this->REST_Return(400, 'invoice lines kosong.', array(
                'registration_id' => $reg->id, 'lines' => count($lines),
            ), 'INVOICE_LINES_EMPTY');
            return;
        }
        // uang_muka + diskon tidak boleh melebihi total (keduanya bagian dari pelunasan).
        if ($uang_muka + $diskon > $total) {
            $this->db->trans_rollback();
            $this->REST_Return(422, 'uang_muka + diskon melebihi total piutang.', array(
                'total_piutang' => $total, 'uang_muka' => $uang_muka, 'diskon' => $diskon,
            ), 'PIUTANG_OVERPAID');
            return;
        }

        $id   = $this->_pj_uuid();
        $sisa = round($total - $uang_muka - $diskon, 2);
        $now  = date('Y-m-d H:i:s');
        // E-2: nomor invoice dibuat server-side (prefix PJ, urut per bulan).
        $invoice_no     = $this->_pj_invoice_no();
        $invoice_status = $sisa <= 0 ? 'paid' : 'open';
        $ins  = $this->db->query(
            "INSERT INTO piutang (id, invoice_no, institute_branch_id, registration_id, registration_destination_id,
                no_rawat, norm, patient_name, insurance_id, company_id, tgl_piutang, tgl_tempo,
                total_piutang, uang_muka, diskon, total_dibayar, sisa_piutang, status, invoice_status, reason,
                created_by, created_at, is_delete)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0)",
            array($id, $invoice_no, $branch, $reg->id, $reg->registration_destination_id, $no_rawat, $reg->norm,
                  $reg->patient_name,
                  isset($dt_post['insurance_id']) ? (int) $dt_post['insurance_id'] : 0,
                  isset($dt_post['company_id']) && $dt_post['company_id'] !== '' ? $dt_post['company_id'] : null,
                  isset($dt_post['tgl_piutang']) && $dt_post['tgl_piutang'] !== '' ? $dt_post['tgl_piutang'] : date('Y-m-d'),
                  isset($dt_post['tgl_tempo']) && $dt_post['tgl_tempo'] !== '' ? $dt_post['tgl_tempo'] : null,
                  $total, $uang_muka, $diskon, 0, $sisa,
                  $sisa <= 0 ? 'paid' : 'open', $invoice_status,
                  isset($dt_post['reason']) ? trim((string) $dt_post['reason']) : null,
                  $user_id, $now));
        if (!$ins) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menyimpan piutang.', array(), 'PIUTANG_CREATE_FAILED');
            return;
        }
        if (!$this->_pj_audit('PIUTANG_CREATED', $id, $user_id, array(
            'no_rawat' => $no_rawat, 'total_piutang' => $total, 'uang_muka' => $uang_muka,
            'diskon' => $diskon, 'sisa_piutang' => $sisa, 'status' => $sisa <= 0 ? 'paid' : 'open',
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit piutang.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }

        // Tahap 1: jurnal otomatis (debit piutang / credit pendapatan).
        // Gagal balance => rollback. Mapping/akun belum ada => skip + audit, transaksi tetap jalan.
        $journal = $this->post_journal('PIUTANG_CREATE', $id, 'PJ-CREATE-' . $id, $branch, $user_id,
            array('receivable' => $total, 'revenue' => $total), $no_rawat);
        if (isset($journal['status']) && $journal['status'] === 'error') {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Jurnal piutang gagal (tidak balance).', $journal, 'JOURNAL_FAILED');
            return;
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Piutang gagal disimpan.', array(), 'PIUTANG_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'id' => $id, 'invoice_no' => $invoice_no, 'no_rawat' => $no_rawat,
            'total_piutang' => $total, 'sisa_piutang' => $sisa,
            'status' => $sisa <= 0 ? 'paid' : 'open', 'invoice_status' => $invoice_status,
        ));
        return;
    }

    /* ===================== POST pay ===================== */
    public function pay_post($p = 0) {
        $data    = $this->json_post();
        $dt_post = $data['dt_postput'];

        $branch = $this->_pj_branch('pay');
        if ($branch === null) { return; }
        $user_id = $this->_pj_user();

        $piutang_id = isset($dt_post['piutang_id']) ? trim((string) $dt_post['piutang_id']) : '';
        $amount     = isset($dt_post['amount']) ? (float) $dt_post['amount'] : 0;
        $discount   = isset($dt_post['discount']) ? (float) $dt_post['discount'] : 0;
        $idem       = isset($dt_post['idempotency_key']) ? trim((string) $dt_post['idempotency_key']) : '';

        if ($piutang_id === '') {
            $this->REST_Return(422, 'piutang_id wajib diisi.', array(), 'PIUTANG_ID_REQUIRED');
            return;
        }
        if ($amount <= 0) {
            $this->REST_Return(422, 'nominal pembayaran harus lebih dari 0.', array(), 'PAYMENT_AMOUNT_INVALID');
            return;
        }
        if ($discount < 0) {
            $this->REST_Return(422, 'diskon tidak boleh negatif.', array(), 'PAYMENT_AMOUNT_INVALID');
            return;
        }
        if ($idem === '') {
            // Idempotency key wajib: tanpa itu klien tidak bisa replay dengan aman.
            $this->REST_Return(422, 'idempotency_key wajib diisi.', array(), 'IDEMPOTENCY_KEY_REQUIRED');
            return;
        }
        if (strlen($idem) > 64) {
            $this->REST_Return(422, 'idempotency_key maksimal 64 karakter.', array(), 'IDEMPOTENCY_KEY_INVALID');
            return;
        }

        $this->db->trans_start();
        $row = $this->db->query(
            "SELECT * FROM piutang WHERE id = ? FOR UPDATE", array($piutang_id))->row();
        if ($row === null) {
            $this->_pj_deny('PIUTANG_NOT_FOUND', $piutang_id, $user_id, 'pay',
                array('reason' => 'not_found'), 404, 'Piutang tidak ditemukan.');
            return;
        }
        if ((int) $row->is_delete === 1) {
            $this->_pj_deny('PIUTANG_DELETED', $piutang_id, $user_id, 'pay',
                array('reason' => 'is_delete'), 409, 'Piutang sudah dihapus.');
            return;
        }
        if ((string) $row->institute_branch_id !== $branch) {
            $this->_pj_deny('PIUTANG_BRANCH_SCOPE_MISMATCH', $piutang_id, $user_id, 'pay',
                array('object_branch' => $row->institute_branch_id, 'token_branch' => $branch), 403,
                'Piutang berada di luar branch scope.');
            return;
        }

        // Idempotensi: replay dengan key sama.
        $prev = $this->db->query(
            "SELECT * FROM piutang_payment WHERE piutang_id = ? AND idempotency_key = ?",
            array($piutang_id, $idem))->row();
        if ($prev !== null) {
            if ((float) $prev->amount === round($amount, 2) && (float) $prev->discount === round($discount, 2)) {
                $this->db->trans_commit();
                $this->REST_Return(200, 'SUCCESS (replay)', array(
                    'payment_id' => $prev->id, 'replayed' => true,
                    'sisa_piutang' => (float) $row->sisa_piutang, 'status' => $row->status,
                ));
                return;
            }
            $this->_pj_deny('PIUTANG_PAYMENT_DUPLICATE', $piutang_id, $user_id, 'pay',
                array('idempotency_key' => $idem, 'existing_payment_id' => $prev->id,
                      'existing_amount' => (float) $prev->amount, 'requested_amount' => $amount), 409,
                'Pembayaran dengan kunci ini sudah tercatat dengan nominal berbeda.');
            return;
        }
        if ($row->status === 'cancelled') {
            $this->_pj_deny('PIUTANG_CANCELLED', $piutang_id, $user_id, 'pay',
                array('status' => $row->status), 409, 'Piutang sudah dibatalkan.');
            return;
        }
        if ($row->status === 'paid') {
            $this->_pj_deny('PIUTANG_ALREADY_PAID', $piutang_id, $user_id, 'pay',
                array('status' => $row->status, 'sisa_piutang' => (float) $row->sisa_piutang), 409,
                'Piutang sudah lunas.');
            return;
        }

        $sisa     = (float) $row->sisa_piutang;
        $potongan = round($amount + $discount, 2);
        if ($potongan > $sisa + 0.001) {
            $this->_pj_deny('PAYMENT_EXCEEDS_SISA', $piutang_id, $user_id, 'pay',
                array('sisa_piutang' => $sisa, 'amount' => $amount, 'discount' => $discount), 409,
                'Pembayaran melebihi sisa piutang.');
            return;
        }

        $pay_id     = $this->_pj_uuid();
        $now        = date('Y-m-d H:i:s');
        $new_sisa   = round($sisa - $potongan, 2);
        $new_status = ($new_sisa <= 0.001) ? 'paid' : 'partial';
        $new_sisa   = ($new_sisa < 0) ? 0.0 : $new_sisa;

        $ok = $this->db->query(
            "INSERT INTO piutang_payment (id, piutang_id, institute_branch_id, amount, discount,
                payment_method_id, cara_bayar, note, paid_at, paid_by, idempotency_key, is_void)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,0)",
            array($pay_id, $piutang_id, $branch, round($amount, 2), round($discount, 2),
                  isset($dt_post['payment_method_id']) && $dt_post['payment_method_id'] !== '' ? $dt_post['payment_method_id'] : null,
                  isset($dt_post['cara_bayar']) ? trim((string) $dt_post['cara_bayar']) : null,
                  isset($dt_post['note']) ? trim((string) $dt_post['note']) : null,
                  $now, $user_id, $idem));
        if (!$ok) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menyimpan pembayaran.', array(), 'PAYMENT_WRITE_FAILED');
            return;
        }
        $upd = $this->db->query(
            "UPDATE piutang
                SET total_dibayar = ROUND(total_dibayar + ?, 2),
                    diskon        = ROUND(diskon + ?, 2),
                    sisa_piutang  = ?,
                    status        = ?,
                    invoice_status = ?,
                    updated_by    = ?,
                    updated_at    = ?
              WHERE id = ? AND sisa_piutang = ?",
            array(round($amount, 2), round($discount, 2), $new_sisa, $new_status, $new_status,
                  $user_id, $now, $piutang_id, $row->sisa_piutang));
        if ($this->db->affected_rows() !== 1) {
            $this->db->trans_rollback();
            $this->REST_Return(409, 'Piutang berubah oleh transaksi lain, ulangi.', array(), 'PIUTANG_CONCURRENT_UPDATE');
            return;
        }
        if (!$this->_pj_audit('PIUTANG_PAYMENT_OK', $piutang_id, $user_id, array(
            'payment_id' => $pay_id, 'no_rawat' => $row->no_rawat, 'amount' => round($amount, 2),
            'discount' => round($discount, 2), 'sisa_before' => $sisa, 'sisa_after' => $new_sisa,
            'status_after' => $new_status, 'idempotency_key' => $idem,
            'cara_bayar' => isset($dt_post['cara_bayar']) ? $dt_post['cara_bayar'] : null,
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit pembayaran.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }

        // Tahap 1: jurnal otomatis (debit kas + diskon / credit piutang).
        $journal = $this->post_journal('PIUTANG_PAYMENT', $pay_id, 'PJ-PAY-' . $pay_id, $branch, $user_id,
            array('cash' => round($amount, 2), 'discount' => round($discount, 2),
                  'receivable' => round($amount + $discount, 2)), $row->no_rawat);
        if (isset($journal['status']) && $journal['status'] === 'error') {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Jurnal pembayaran gagal (tidak balance).', $journal, 'JOURNAL_FAILED');
            return;
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Pembayaran gagal disimpan.', array(), 'PAYMENT_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'payment_id' => $pay_id, 'piutang_id' => $piutang_id, 'amount' => round($amount, 2),
            'discount' => round($discount, 2), 'sisa_piutang' => $new_sisa,
            'status' => $new_status, 'no_rawat' => $row->no_rawat,
        ));
        return;
    }

    /* ===================== POST return (batalkan) ===================== */
    public function return_post($p = 0) {
        $data    = $this->json_post();
        $dt_post = $data['dt_postput'];

        $branch = $this->_pj_branch('return');
        if ($branch === null) { return; }
        $user_id = $this->_pj_user();

        $piutang_id = isset($dt_post['piutang_id']) ? trim((string) $dt_post['piutang_id']) : '';
        $reason     = isset($dt_post['reason']) ? trim((string) $dt_post['reason']) : '';
        if ($piutang_id === '') {
            $this->REST_Return(422, 'piutang_id wajib diisi.', array(), 'PIUTANG_ID_REQUIRED');
            return;
        }
        if ($reason === '') {
            $this->REST_Return(422, 'alasan pembatalan wajib diisi.', array(), 'REASON_REQUIRED');
            return;
        }

        $this->db->trans_start();
        $row = $this->db->query("SELECT * FROM piutang WHERE id = ? FOR UPDATE", array($piutang_id))->row();
        if ($row === null) {
            $this->_pj_deny('PIUTANG_NOT_FOUND', $piutang_id, $user_id, 'return',
                array('reason' => 'not_found'), 404, 'Piutang tidak ditemukan.');
            return;
        }
        if ((string) $row->institute_branch_id !== $branch) {
            $this->_pj_deny('PIUTANG_BRANCH_SCOPE_MISMATCH', $piutang_id, $user_id, 'return',
                array('object_branch' => $row->institute_branch_id, 'token_branch' => $branch), 403,
                'Piutang berada di luar branch scope.');
            return;
        }
        if ($row->status === 'cancelled') {
            $this->_pj_deny('PIUTANG_ALREADY_CANCELLED', $piutang_id, $user_id, 'return',
                array('status' => $row->status), 409, 'Piutang sudah dibatalkan.');
            return;
        }
        if ((float) $row->total_dibayar > 0) {
            $this->_pj_deny('PIUTANG_HAS_PAYMENT', $piutang_id, $user_id, 'return',
                array('total_dibayar' => (float) $row->total_dibayar), 409,
                'Piutang sudah memiliki pembayaran; void pembayaran lebih dulu.');
            return;
        }
        $this->db->query(
            "UPDATE piutang SET status = 'cancelled', invoice_status = 'cancelled', reason = ?, updated_by = ?, updated_at = ? WHERE id = ?",
            array($reason, $user_id, date('Y-m-d H:i:s'), $piutang_id));
        if (!$this->_pj_audit('PIUTANG_CANCELLED', $piutang_id, $user_id, array(
            'no_rawat' => $row->no_rawat, 'reason' => $reason,
            'sisa_piutang' => (float) $row->sisa_piutang,
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit pembatalan.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Pembatalan gagal disimpan.', array(), 'PIUTANG_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'piutang_id' => $piutang_id, 'status' => 'cancelled', 'no_rawat' => $row->no_rawat,
        ));
        return;
    }

    /* ===================== GET report ===================== */
    public function report_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_pj_branch('report');
        if ($branch === null) { return; }

        $start = isset($q['date_start']) && $q['date_start'] !== '' ? $q['date_start'] : date('Y-m-01');
        $end   = isset($q['date_end']) && $q['date_end'] !== '' ? $q['date_end'] : date('Y-m-d');

        $sum = $this->db->query(
            "SELECT COUNT(*) AS n,
                    IFNULL(SUM(total_piutang),0) AS total,
                    IFNULL(SUM(uang_muka),0)     AS uang_muka,
                    IFNULL(SUM(diskon),0)        AS diskon,
                    IFNULL(SUM(total_dibayar),0) AS dibayar,
                    IFNULL(SUM(sisa_piutang),0)  AS sisa
               FROM piutang
              WHERE institute_branch_id = ? AND is_delete = 0
                AND tgl_piutang BETWEEN ? AND ?",
            array($branch, $start, $end))->row();
        $by_status = $this->db->query(
            "SELECT status, COUNT(*) AS n, IFNULL(SUM(sisa_piutang),0) AS sisa
               FROM piutang
              WHERE institute_branch_id = ? AND is_delete = 0
                AND tgl_piutang BETWEEN ? AND ?
              GROUP BY status ORDER BY status",
            array($branch, $start, $end))->result();
        $pay = $this->db->query(
            "SELECT COUNT(*) AS n, IFNULL(SUM(amount),0) AS amount, IFNULL(SUM(discount),0) AS discount
               FROM piutang_payment
              WHERE institute_branch_id = ? AND is_void = 0
                AND DATE(paid_at) BETWEEN ? AND ?",
            array($branch, $start, $end))->row();

        $st = array();
        foreach ($by_status as $s) {
            $st[] = array('status' => $s->status, 'count' => (int) $s->n, 'sisa' => (float) $s->sisa);
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'period'      => array('start' => $start, 'end' => $end),
            'branch'      => $branch,
            'piutang'     => array(
                'count' => (int) $sum->n, 'total' => (float) $sum->total,
                'uang_muka' => (float) $sum->uang_muka, 'diskon' => (float) $sum->diskon,
                'dibayar' => (float) $sum->dibayar, 'sisa' => (float) $sum->sisa,
            ),
            'payments'    => array(
                'count' => (int) $pay->n, 'amount' => (float) $pay->amount,
                'discount' => (float) $pay->discount,
            ),
            'by_status'   => $st,
        ));
        return;
    }

    /* ===================== GET invoice (E-2) ===================== */

    /**
     * Detail invoice piutang untuk satu kunjungan / satu piutang.
     * GET v1/finance/piutang/invoice?id=<piutang_id>
     * GET v1/finance/piutang/invoice?no_rawat=<no_rawat>
     *
     * Mengembalikan header invoice + rincian tagihan (dari transaction_invoice
     * bila kasir sudah membuatnya) + daftar pembayaran. Dipakai halaman piutang
     * untuk mencetak invoice/kwitansi.
     */
    public function invoice_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_pj_branch('invoice');
        if ($branch === null) { return; }

        // $p default 0 (bukan '') sehingga harus dinormalkan: kalau tidak, "0"
        // dianggap id dan query by no_rawat tidak pernah jalan.
        $pkey     = trim((string) $p);
        $id       = isset($q['id']) ? trim((string) $q['id']) : ($pkey === '0' ? '' : $pkey);
        $no_rawat = isset($q['no_rawat']) ? trim((string) $q['no_rawat']) : '';
        if ($id === '' && $no_rawat === '') {
            $this->REST_Return(422, 'id atau no_rawat wajib diisi.', array(), 'INVOICE_KEY_REQUIRED');
            return;
        }

        $where  = "p.institute_branch_id = ? AND p.is_delete = 0";
        $params = array($branch);
        if ($id !== '') {
            $where .= " AND p.id = ?";
            $params[] = $id;
        } else {
            $where .= " AND p.no_rawat = ?";
            $params[] = $no_rawat;
        }

        $row = $this->db->query(
            "SELECT p.*, c.name AS company_name, r.code AS registration_code
               FROM piutang p
               LEFT JOIN company c ON c.id = p.company_id
               LEFT JOIN registration r ON r.id = p.registration_id
              WHERE $where
              ORDER BY p.created_at DESC LIMIT 1", $params)->row();
        if ($row === null) {
            $this->REST_Return(404, 'Invoice piutang tidak ditemukan.', array(), 'PIUTANG_INVOICE_NOT_FOUND');
            return;
        }

        // Rincian tagihan: sumber tunggal yang sama dengan auto-calc total (E-2).
        $items = $this->_pj_invoice_lines($row->registration_id);
        $inv = $this->db->query(
            "SELECT id, code, date_added, payment_id FROM transaction_invoice
              WHERE registration_id = ? AND IFNULL(is_delete,0) = 0
              ORDER BY date_added DESC LIMIT 1", array($row->registration_id))->row();

        $pays = $this->db->query(
            "SELECT id, amount, discount, cara_bayar, note, paid_at, paid_by
               FROM piutang_payment
              WHERE piutang_id = ? AND IFNULL(is_void,0) = 0
              ORDER BY paid_at", array($row->id))->result();

        $lines = array();
        $total_lines = 0.0;
        foreach ($items as $it) {
            $lines[] = array(
                'type'   => (int) $it['type'],
                'name'   => $it['name'],
                'amount' => (float) $it['amount'],
            );
            $total_lines += (float) $it['amount'];
        }
        $total_lines = round($total_lines, 2);
        $payments = array();
        foreach ($pays as $x) {
            $payments[] = array(
                'payment_id' => $x->id, 'amount' => (float) $x->amount, 'discount' => (float) $x->discount,
                'cara_bayar' => $x->cara_bayar, 'note' => $x->note, 'paid_at' => $x->paid_at,
            );
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'invoice_no'     => $row->invoice_no,
            'invoice_status' => $row->invoice_status,
            'piutang_id'     => $row->id,
            'no_rawat'       => $row->no_rawat,
            'registration_code' => $row->registration_code,
            'norm'           => $row->norm,
            'patient_name'   => $row->patient_name,
            'company_name'   => $row->company_name,
            'tgl_piutang'    => $row->tgl_piutang,
            'tgl_tempo'      => $row->tgl_tempo,
            'kasir_invoice'  => $inv === null ? null : array('id' => $inv->id, 'code' => $inv->code,
                                                             'date_added' => $inv->date_added,
                                                             'paid' => $inv->payment_id !== null),
            'lines'          => $lines,
            'lines_total'    => $total_lines,
            // Ringkasan siap-tampil: total WAJIB sama dengan SUM(lines).
            'summary'        => array(
                'subtotal'      => $total_lines,
                'tax'           => 0.0,
                'total_piutang' => (float) $row->total_piutang,
                'dibayar'       => (float) $row->total_dibayar,
                'sisa'          => (float) $row->sisa_piutang,
                'status'        => strtoupper((string) $row->invoice_status),
                'lines_match'   => abs($total_lines - (float) $row->total_piutang) < 0.005,
            ),
            'total_piutang'  => (float) $row->total_piutang,
            'uang_muka'      => (float) $row->uang_muka,
            'diskon'         => (float) $row->diskon,
            'total_dibayar'  => (float) $row->total_dibayar,
            'sisa_piutang'   => (float) $row->sisa_piutang,
            'status'         => $row->status,
            'payments'       => $payments,
        ));
        return;
    }

    /* ===================== POST recalc (E-2) ===================== */

    /**
     * Hitung ulang total_piutang dari baris invoice. Dipakai ketika baris invoice
     * berubah setelah piutang dibuat (tidak ada kolom total yang bisa diedit manual).
     * POST v1/finance/piutang/recalc  {piutang_id}
     */
    public function recalc_post($p = 0) {
        header("Access-Control-Allow-Origin: *");
        $data    = $this->json_post();
        $dt_post = isset($data['dt_postput']) ? $data['dt_postput'] : $data;

        $branch = $this->_pj_branch('recalc');
        if ($branch === null) { return; }
        $user_id = $this->_pj_user();

        $piutang_id = isset($dt_post['piutang_id']) ? trim((string) $dt_post['piutang_id']) : '';
        if ($piutang_id === '') {
            $this->REST_Return(422, 'piutang_id wajib diisi.', array(), 'PIUTANG_KEY_REQUIRED');
            return;
        }

        $this->db->trans_start();
        $row = $this->db->query(
            "SELECT * FROM piutang WHERE id = ? AND institute_branch_id = ? AND is_delete = 0 FOR UPDATE",
            array($piutang_id, $branch))->row();
        if ($row === null) {
            $this->_pj_deny('PIUTANG_NOT_FOUND', $piutang_id, $user_id, 'recalc', array(), 404,
                'Piutang tidak ditemukan.');
            return;
        }
        if ($row->status === 'cancelled') {
            $this->_pj_deny('PIUTANG_CANCELLED', $piutang_id, $user_id, 'recalc', array(), 409,
                'Piutang sudah dibatalkan.');
            return;
        }

        $lines = $this->_pj_invoice_lines($row->registration_id);
        $total = 0.0;
        foreach ($lines as $l) { $total += (float) $l['amount']; }
        $total = round($total, 2);
        if (!$lines || $total <= 0) {
            $this->db->trans_rollback();
            $this->REST_Return(400, 'invoice lines kosong.', array('lines' => count($lines)),
                'INVOICE_LINES_EMPTY');
            return;
        }
        $paid = round((float) $row->total_dibayar + (float) $row->diskon, 2);
        if ($total + 0.005 < $paid) {
            $this->db->trans_rollback();
            $this->REST_Return(400, 'total baris lebih kecil dari yang sudah dibayar.', array(
                'total_lines' => $total, 'dibayar' => $paid,
            ), 'PIUTANG_TOTAL_BELOW_PAID');
            return;
        }
        $sisa = round($total - (float) $row->uang_muka - (float) $row->diskon - (float) $row->total_dibayar, 2);
        if ($sisa < 0) { $sisa = 0.0; }
        $st = $sisa <= 0 ? 'paid' : ((float) $row->total_dibayar > 0 ? 'partial' : 'open');
        $this->db->query(
            "UPDATE piutang SET total_piutang = ?, sisa_piutang = ?, status = ?, invoice_status = ?,
                    updated_by = ?, updated_at = ? WHERE id = ?",
            array($total, $sisa, $st, $st, $user_id, date('Y-m-d H:i:s'), $piutang_id));
        $this->_pj_audit('PIUTANG_RECALC', $piutang_id, $user_id, array(
            'total_lama' => (float) $row->total_piutang, 'total_baru' => $total,
            'lines' => count($lines), 'sisa' => $sisa,
        ));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Gagal hitung ulang piutang.', array(), 'PIUTANG_RECALC_FAILED');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'piutang_id' => $piutang_id, 'total_piutang' => $total,
            'sisa_piutang' => $sisa, 'status' => $st, 'lines' => count($lines),
        ));
    }

    /* ===================== GET kwitansi (E-2) ===================== */

    /**
     * Data kwitansi untuk satu pembayaran piutang.
     * GET v1/finance/piutang/kwitansi?payment_id=<id>
     */
    public function kwitansi_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_pj_branch('kwitansi');
        if ($branch === null) { return; }

        $pkey       = trim((string) $p);
        $payment_id = isset($q['payment_id']) ? trim((string) $q['payment_id'])
                                              : ($pkey === '0' ? '' : $pkey);
        if ($payment_id === '') {
            $this->REST_Return(422, 'payment_id wajib diisi.', array(), 'PAYMENT_ID_REQUIRED');
            return;
        }

        $row = $this->db->query(
            "SELECT pp.*, p.invoice_no, p.no_rawat, p.norm, p.patient_name,
                    p.total_piutang, p.sisa_piutang, p.status, p.invoice_status
               FROM piutang_payment pp
               INNER JOIN piutang p ON p.id = pp.piutang_id
              WHERE pp.id = ? AND pp.institute_branch_id = ?", array($payment_id, $branch))->row();
        if ($row === null) {
            $this->REST_Return(404, 'Kwitansi tidak ditemukan.', array(), 'KWITANSI_NOT_FOUND');
            return;
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'payment_id'    => $row->id,
            'invoice_no'    => $row->invoice_no,
            'no_rawat'      => $row->no_rawat,
            'norm'          => $row->norm,
            'patient_name'  => $row->patient_name,
            'amount'        => (float) $row->amount,
            'discount'      => (float) $row->discount,
            'cara_bayar'    => $row->cara_bayar,
            'note'          => $row->note,
            'paid_at'       => $row->paid_at,
            'total_piutang' => (float) $row->total_piutang,
            'sisa_piutang'  => (float) $row->sisa_piutang,
            'status'        => $row->status,
            'invoice_status' => $row->invoice_status,
        ));
        return;
    }

    /* ===================== helpers ===================== */

    /**
     * E-2: baris invoice (sumber tunggal total_piutang). Dipakai create_post untuk
     * auto-calc dan invoice_get untuk rincian, supaya angka tidak pernah beda.
     * amount = var_1 pada tabel detail sesuai type_transaction_id.
     */
    protected function _pj_invoice_lines($registration_id) {
        $inv = $this->db->query(
            "SELECT id FROM transaction_invoice
              WHERE registration_id = ? AND IFNULL(is_delete,0) = 0
              ORDER BY date_added DESC LIMIT 1", array($registration_id))->row();
        if ($inv === null) { return array(); }
        $rows = $this->db->query(
            "SELECT tii.type_transaction_id AS type,
                    COALESCE(li.costing_name, ri.costing_name, ta.costing_name, td.costing_name,
                             CONCAT('Kamar ', IFNULL(tb.bed_name,'')), pc.name, '') AS item_name,
                    COALESCE(li.var_1, ri.var_1, ta.var_1, td.var_1, tb.var_3,
                             (pi.qty - IFNULL(pi.qty_retur,0)) * IFNULL(pi.price_sale,0), 0) AS amount
               FROM transaction_invoice_item tii
               -- Join murni lewat id (UUID unik per tabel): tahan terhadap dua konvensi
               -- type_transaction_id yang beredar (lab 11 vs 12, rad 12 vs 15).
               LEFT JOIN laboratory_request_item li ON li.id = tii.transaction_id
               LEFT JOIN radiology_request_item  ri ON ri.id = tii.transaction_id
               LEFT JOIN transaction_administration ta ON ta.id = tii.transaction_id
               LEFT JOIN transaction_doctor       td ON td.id = tii.transaction_id
               -- INAP-CLOSE: kamar inap = transaction_bed, nilainya di var_3
               -- (diisi saat closing: harga kelas x jumlah hari).
               LEFT JOIN transaction_bed          tb ON tb.id = tii.transaction_id
               -- E-3: obat (type 14). transaction_id = pharmacy_prescription_id, jadi satu
               -- baris invoice_item bisa memuat banyak baris resep -> satu baris per item.
               -- qty bersih = qty - qty_retur, harga = snapshot price_sale saat dispensing.
               LEFT JOIN pharmacy_prescription_item pi ON pi.pharmacy_prescription_id = tii.transaction_id
                    AND tii.type_transaction_id = 14 AND IFNULL(pi.is_delete,0) = 0
               LEFT JOIN pharmacy_catalog pc ON pc.id = pi.pharmacy_catalog_id AND tii.type_transaction_id = 14
              WHERE tii.transaction_invoice_id = ? AND IFNULL(tii.is_delete,0) = 0",
            array($inv->id))->result();
        $out = array();
        foreach ($rows as $r) {
            $out[] = array('type' => (int) $r->type, 'name' => (string) $r->item_name,
                           'amount' => (float) $r->amount);
        }
        return $out;
    }

    /**
     * E-2: nomor invoice piutang, urut per bulan, memakai counter FO yang sama
     * dengan invoice kasir (library Transaction / tabel counter_queue).
     * Format: dd/mm/PJ/00001/YYYY
     */
    protected function _pj_invoice_no() {
        $counter = array(
            'var_1' => 'PJ',
            'var_2' => date('Y'),
            'var_3' => date('m'),
            'var_5' => date('Y-m'),
        );
        $this->load->library('transaction');
        $c = $this->transaction->counter($counter);
        if (isset($c['counter_queue']) && (string) $c['counter_queue'] === '0') {
            $this->load->model('Counter_queue_model', 'Counter_queue');
            $this->Counter_queue->replace(array(
                'var_1' => 'PJ', 'var_2' => date('Y'), 'var_3' => date('m'),
                'var_5' => date('Y-m'), 'counter_queue' => 0,
            ));
            $c = $this->transaction->counter($counter);
        }
        $seq = isset($c['counter_queue']) ? (int) $c['counter_queue'] : 1;
        return date('d') . '/' . date('m') . '/PJ/' . str_pad($seq, 5, '0', STR_PAD_LEFT) . '/' . date('Y');
    }

    /** Branch dari token; kosong/ tak valid => tolak (fail-closed). */
    protected function _pj_branch($operation) {
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
            $this->_pj_audit('DENIED_BRANCH_SCOPE_UNDEFINED', null,
                $this->security->xss_clean($t['data']->id),
                array('reason' => 'branch_empty', 'operation' => $operation), 409);
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return null;
        }
        return $branch;
    }

    protected function _pj_user() {
        return isset($this->s1_actor['user_id']) ? $this->s1_actor['user_id'] : null;
    }

    protected function _pj_deny($code, $piutang_id, $user_id, $operation, $detail, $http, $message) {
        $this->db->trans_rollback();
        $this->_pj_audit('DENIED_' . $code, $piutang_id, $user_id,
            array_merge(array('operation' => $operation,
                              'applied_by' => $user_id,
                              'applied_from_branch' => isset($this->s1_actor['branch']) ? $this->s1_actor['branch'] : null),
                        (array) $detail), $http);
        $this->REST_Return($http, $message, (array) $detail, $code);
    }

    /** Audit ke log_activity (append-only; tanpa DDL baru). */
    protected function _pj_audit($result, $piutang_id, $user_id, $detail = array(), $http = 200) {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        $audit_id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
        $desc = json_encode(array_merge(array(
            'request_id' => $this->debug_uniq,
            'endpoint'   => 'finance/piutang',
            'action'     => strtolower(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'),
            'result'     => $result,
            'http_status'=> $http,
            'piutang_id' => $piutang_id,
            'branch'     => isset($this->s1_actor['branch']) ? $this->s1_actor['branch'] : null,
        ), (array) $detail));
        $ok = $this->db->query(
            "INSERT INTO log_activity (id, noregistrasi, user_id, user_name, category_id, description,
                related_id, related_table, created_at) VALUES (?,?,?,?,?,?,?,?,?)",
            array($audit_id, null, $user_id,
                  isset($this->s1_actor['username']) ? $this->s1_actor['username'] : null,
                  0, $desc, $piutang_id, 'piutang', date('Y-m-d H:i:s')));
        if (!$ok) {
            Logger::error($this->debug_uniq, 'Piutang audit write failed: ' . $result);
        }
        return (bool) $ok;
    }

    protected function _pj_uuid() {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
    }

    /**
     * Tahap 1: jurnal otomatis. 'skipped' (mapping/akun belum ada) DIAUDIT supaya
     * kelihatan, tetapi tidak menggagalkan transaksi bisnis. 'error' => pemanggil rollback.
     */
    protected function post_journal($type, $ref_id, $ref_no, $branch, $user_id, $amounts, $note = '') {
        $this->load->library('Journal');
        $res = $this->journal->post($type, $ref_id, $ref_no, $branch, $user_id, $amounts, null, (string) $note);
        if (isset($res['status']) && $res['status'] !== 'posted' && $res['status'] !== 'duplicate') {
            $this->_pj_audit('JOURNAL_' . strtoupper($res['status']), $ref_id, $user_id, $res, 200);
        }
        return $res;
    }
}
