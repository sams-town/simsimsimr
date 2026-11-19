<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;

/**
 * SURGERY — pelengkap modul operasi (isolated).
 *
 * Booking & operasi sudah ada di Operating_room (create/update/approve/operator/
 * report klinis). Controller ini menambahkan empat hal yang belum ada padanannya,
 * diturunkan dari alur Khanza (DlgBookingOperasi / DlgTagihanOperasi /
 * RMChecklistPreOperasi / RMChecklistPostOperasi / DlgOperasiPerBulan):
 *
 *   GET    v1/health_facility/surgery/checklist?id=<operating_room_id>
 *   POST   v1/health_facility/surgery/checklist         (simpan pre/post)
 *   GET    v1/health_facility/surgery/consumable?id=...
 *   POST   v1/health_facility/surgery/consumable         (obat/alat dipakai)
 *   POST   v1/health_facility/surgery/consumablevoid    (koreksi + alasan)
 *   GET    v1/health_facility/surgery/fee?id=...
 *   POST   v1/health_facility/surgery/fee               (bayar jasa dokter)
 *   GET    v1/health_facility/surgery/report            (rekap periode)
 *
 * Aturan yang ditegakkan server-side:
 *   - branch HANYA dari token; kosong / objek beda branch => tolak (fail-closed)
 *   - harga obat/alat TIDAK dari client: diambil dari pharmacy_catalog_price
 *   - fee: satu pembayaran per (operasi, role, dokter) + idempotency_key
 *   - setiap aksi & penolakan diaudit (log_activity, related_table='operating_room')
 */
class Surgery extends My_Controller {

    /** Role jasa medik yang boleh dibayar (mengikuti bayar_operasi_* Khanza). */
    private $fee_roles = array('operator1', 'operator2', 'operator3',
        'dokter_anestesi', 'dokter_anak', 'dokter_umum', 'dokter_pjanak');

    public function __construct() {
        parent::__construct();
        $this->load->library('Authorization_Token');
    }

    /* ===================== checklist ===================== */
    public function checklist_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_sg_branch('checklist_get');
        if ($branch === null) { return; }
        $ok_id = isset($q['id']) ? trim((string) $q['id']) : '';
        if (!$this->_sg_scope($ok_id, $branch, 'checklist_get')) { return; }

        $rows = $this->db->query(
            "SELECT id, phase, items, notes, checked_by, checked_at
               FROM operating_room_checklist
              WHERE operating_room_id = ? AND institute_branch_id = ? AND is_delete = 0
              ORDER BY phase", array($ok_id, $branch))->result();
        $out = array('pre' => null, 'post' => null);
        foreach ($rows as $r) {
            $items = json_decode($r->items, true);
            $out[$r->phase] = array(
                'id' => $r->id, 'phase' => $r->phase,
                'items' => is_array($items) ? $items : array(),
                'notes' => $r->notes, 'checked_by' => $r->checked_by, 'checked_at' => $r->checked_at,
            );
        }
        $this->REST_Return(200, 'SUCCESS', $out);
        return;
    }

    public function checklist_post($p = 0) {
        $data    = $this->json_post();
        $dt_post = $data['dt_postput'];

        $branch = $this->_sg_branch('checklist_save');
        if ($branch === null) { return; }
        $user_id = $this->_sg_user();

        $ok_id = isset($dt_post['operating_room_id']) ? trim((string) $dt_post['operating_room_id']) : '';
        $phase = isset($dt_post['phase']) ? strtolower(trim((string) $dt_post['phase'])) : '';
        if (!$this->_sg_scope($ok_id, $branch, 'checklist_save')) { return; }
        if (!in_array($phase, array('pre', 'post'), true)) {
            $this->REST_Return(422, 'phase harus pre atau post.', array(), 'CHECKLIST_PHASE_INVALID');
            return;
        }
        if (!isset($dt_post['items']) || !is_array($dt_post['items'])) {
            $this->REST_Return(422, 'items wajib berupa objek/array.', array(), 'CHECKLIST_ITEMS_INVALID');
            return;
        }
        $items = json_encode($dt_post['items']);
        if ($items === false || strlen($items) > 20000) {
            $this->REST_Return(422, 'items tidak dapat disimpan (bukan JSON valid / terlalu besar).',
                array(), 'CHECKLIST_ITEMS_INVALID');
            return;
        }
        $notes = isset($dt_post['notes']) ? substr(trim((string) $dt_post['notes']), 0, 500) : null;

        $this->db->trans_start();
        $prev = $this->db->query(
            "SELECT id, items FROM operating_room_checklist
              WHERE operating_room_id = ? AND phase = ?", array($ok_id, $phase))->row();
        $now = date('Y-m-d H:i:s');
        if ($prev === null) {
            $cid = $this->_sg_uuid();
            $ok = $this->db->query(
                "INSERT INTO operating_room_checklist (id, operating_room_id, institute_branch_id,
                    phase, items, notes, checked_by, checked_at, is_delete)
                 VALUES (?,?,?,?,?,?,?,?,0)",
                array($cid, $ok_id, $branch, $phase, $items, $notes, $user_id, $now));
        } else {
            $cid = $prev->id;
            $ok = $this->db->query(
                "UPDATE operating_room_checklist
                    SET items = ?, notes = ?, checked_by = ?, checked_at = ?, is_delete = 0
                  WHERE id = ? AND institute_branch_id = ?",
                array($items, $notes, $user_id, $now, $cid, $branch));
        }
        if (!$ok) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menyimpan checklist.', array(), 'CHECKLIST_WRITE_FAILED');
            return;
        }
        if (!$this->_sg_audit($phase === 'pre' ? 'CHECKLIST_PRE_SAVED' : 'CHECKLIST_POST_SAVED',
                $ok_id, $user_id, array(
                    'checklist_id' => $cid, 'phase' => $phase,
                    'item_count' => count($dt_post['items']),
                ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit checklist.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Checklist gagal disimpan.', array(), 'CHECKLIST_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array('id' => $cid, 'phase' => $phase,
            'item_count' => count($dt_post['items'])));
        return;
    }

    /* ===================== consumable (obat/alat) ===================== */
    public function consumable_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_sg_branch('consumable_get');
        if ($branch === null) { return; }
        $ok_id = isset($q['id']) ? trim((string) $q['id']) : '';
        if (!$this->_sg_scope($ok_id, $branch, 'consumable_get')) { return; }

        $rows = $this->db->query(
            "SELECT c.*, pc.name AS catalog_name, pc.code AS catalog_code
               FROM operating_room_consumable c
               LEFT JOIN pharmacy_catalog pc ON pc.id = c.pharmacy_catalog_id
              WHERE c.operating_room_id = ? AND c.institute_branch_id = ? AND c.is_delete = 0
              ORDER BY c.added_at", array($ok_id, $branch))->result();
        $total = 0.0;
        $out = array();
        foreach ($rows as $r) {
            $total += (float) $r->total_price;
            $out[] = array(
                'id' => $r->id, 'pharmacy_catalog_id' => $r->pharmacy_catalog_id,
                'catalog_name' => $r->catalog_name, 'catalog_code' => $r->catalog_code,
                'quantity' => (float) $r->quantity, 'unit_price' => (float) $r->unit_price,
                'total_price' => (float) $r->total_price, 'price_source' => $r->price_source,
                'added_at' => $r->added_at,
            );
        }
        $this->REST_Return(200, 'SUCCESS', array('rows' => $out, 'total' => round($total, 2)));
        return;
    }

    public function consumable_post($p = 0) {
        $data    = $this->json_post();
        $dt_post = $data['dt_postput'];

        $branch = $this->_sg_branch('consumable_add');
        if ($branch === null) { return; }
        $user_id = $this->_sg_user();

        $ok_id = isset($dt_post['operating_room_id']) ? trim((string) $dt_post['operating_room_id']) : '';
        $catalog_id = isset($dt_post['pharmacy_catalog_id']) ? trim((string) $dt_post['pharmacy_catalog_id']) : '';
        $qty = isset($dt_post['quantity']) ? (float) $dt_post['quantity'] : 0;
        if (!$this->_sg_scope($ok_id, $branch, 'consumable_add')) { return; }
        if ($catalog_id === '') {
            $this->REST_Return(422, 'pharmacy_catalog_id wajib diisi.', array(), 'CATALOG_ID_REQUIRED');
            return;
        }
        if ($qty <= 0) {
            $this->REST_Return(422, 'quantity harus lebih dari 0.', array(), 'QUANTITY_INVALID');
            return;
        }

        // Harga TIDAK dari client: ambil dari snapshot harga server-side (4C-03).
        $price = $this->db->query(
            "SELECT id, price_sale
               FROM pharmacy_catalog_price
              WHERE pharmacy_catalog_id = ? AND institute_branch_id = ?
                AND is_active = 1
                AND (valid_from IS NULL OR valid_from <= CURDATE())
                AND (valid_to IS NULL OR valid_to >= CURDATE())
              ORDER BY valid_from DESC, version DESC
              LIMIT 1", array($catalog_id, $branch))->row();
        if ($price === null) {
            $this->_sg_audit('DENIED_SURGERY_PRICE_MISSING', $ok_id, $user_id,
                array('pharmacy_catalog_id' => $catalog_id, 'operation' => 'consumable_add'), 409);
            $this->REST_Return(409, 'Harga obat/alat belum tersedia di katalog branch ini.',
                array('pharmacy_catalog_id' => $catalog_id), 'SURGERY_PRICE_MISSING');
            return;
        }
        $unit_price = round((float) $price->price_sale, 4);
        $total      = round($unit_price * $qty, 2);

        $this->db->trans_start();
        $cid = $this->_sg_uuid();
        $ok = $this->db->query(
            "INSERT INTO operating_room_consumable (id, operating_room_id, institute_branch_id,
                pharmacy_catalog_id, quantity, unit_price, total_price, price_source,
                price_catalog_id, added_by, added_at, is_delete)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,0)",
            array($cid, $ok_id, $branch, $catalog_id, $qty, $unit_price, $total,
                  'pharmacy_catalog_price', $price->id, $user_id, date('Y-m-d H:i:s')));
        if (!$ok) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menyimpan pemakaian obat/alat.', array(), 'CONSUMABLE_WRITE_FAILED');
            return;
        }
        if (!$this->_sg_audit('SURGERY_CONSUMABLE_ADDED', $ok_id, $user_id, array(
            'consumable_id' => $cid, 'pharmacy_catalog_id' => $catalog_id,
            'quantity' => $qty, 'unit_price' => $unit_price, 'total_price' => $total,
            'price_catalog_id' => $price->id,
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit pemakaian.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Pemakaian gagal disimpan.', array(), 'CONSUMABLE_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array('id' => $cid, 'unit_price' => $unit_price,
            'total_price' => $total));
        return;
    }

    public function consumablevoid_post($p = 0) {
        $data    = $this->json_post();
        $dt_post = $data['dt_postput'];

        $branch = $this->_sg_branch('consumable_void');
        if ($branch === null) { return; }
        $user_id = $this->_sg_user();

        $id     = isset($dt_post['id']) ? trim((string) $dt_post['id']) : '';
        $reason = isset($dt_post['reason']) ? trim((string) $dt_post['reason']) : '';
        if ($id === '') {
            $this->REST_Return(422, 'id wajib diisi.', array(), 'CONSUMABLE_ID_REQUIRED');
            return;
        }
        if ($reason === '') {
            $this->REST_Return(422, 'alasan koreksi wajib diisi.', array(), 'REASON_REQUIRED');
            return;
        }

        $row = $this->db->query(
            "SELECT id, operating_room_id, total_price FROM operating_room_consumable
              WHERE id = ? AND institute_branch_id = ? AND is_delete = 0",
            array($id, $branch))->row();
        if ($row === null) {
            $this->_sg_audit('DENIED_CONSUMABLE_NOT_FOUND', null, $user_id,
                array('consumable_id' => $id, 'operation' => 'consumable_void'), 404);
            $this->REST_Return(404, 'Pemakaian tidak ditemukan.', array(), 'CONSUMABLE_NOT_FOUND');
            return;
        }
        if (!$this->_sg_scope($row->operating_room_id, $branch, 'consumable_void')) { return; }

        // Koreksi (bukan DELETE): baris tetap ada, ditandai is_delete + alasan + audit.
        // DELETE HTTP sengaja TIDAK dipakai untuk data ini (lihat delete_policy P1-b).
        $this->db->trans_start();
        $this->db->query("UPDATE operating_room_consumable SET is_delete = 1 WHERE id = ? AND institute_branch_id = ?",
            array($id, $branch));
        if (!$this->_sg_audit('SURGERY_CONSUMABLE_VOIDED', $row->operating_room_id, $user_id, array(
            'consumable_id' => $id, 'reason' => $reason, 'total_price' => (float) $row->total_price,
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Koreksi gagal disimpan.', array(), 'CONSUMABLE_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array('id' => $id, 'is_delete' => 1, 'reason' => $reason));
        return;
    }

    /* ===================== fee (jasa dokter) ===================== */
    public function fee_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_sg_branch('fee_get');
        if ($branch === null) { return; }
        $ok_id = isset($q['id']) ? trim((string) $q['id']) : '';
        if (!$this->_sg_scope($ok_id, $branch, 'fee_get')) { return; }

        $rows = $this->db->query(
            "SELECT f.*, e.fullname AS employee_name
               FROM operating_room_fee f
               LEFT JOIN employee e ON e.id = f.employee_id
              WHERE f.operating_room_id = ? AND f.institute_branch_id = ? AND f.is_void = 0
              ORDER BY f.paid_at", array($ok_id, $branch))->result();
        $total = 0.0;
        $out = array();
        foreach ($rows as $r) {
            $total += (float) $r->amount;
            $out[] = array('id' => $r->id, 'role' => $r->role, 'employee_id' => $r->employee_id,
                'employee_name' => $r->employee_name, 'amount' => (float) $r->amount,
                'paid_at' => $r->paid_at);
        }
        $this->REST_Return(200, 'SUCCESS', array('rows' => $out, 'total' => round($total, 2)));
        return;
    }

    public function fee_post($p = 0) {
        $data    = $this->json_post();
        $dt_post = $data['dt_postput'];

        $branch = $this->_sg_branch('fee_pay');
        if ($branch === null) { return; }
        $user_id = $this->_sg_user();

        $ok_id     = isset($dt_post['operating_room_id']) ? trim((string) $dt_post['operating_room_id']) : '';
        $role      = isset($dt_post['role']) ? strtolower(trim((string) $dt_post['role'])) : '';
        $employee  = isset($dt_post['employee_id']) ? trim((string) $dt_post['employee_id']) : '';
        $amount    = isset($dt_post['amount']) ? (float) $dt_post['amount'] : 0;
        $idem      = isset($dt_post['idempotency_key']) ? trim((string) $dt_post['idempotency_key']) : '';

        if (!$this->_sg_scope($ok_id, $branch, 'fee_pay')) { return; }
        if (!in_array($role, $this->fee_roles, true)) {
            $this->REST_Return(422, 'role tidak dikenal.', array('allowed' => $this->fee_roles),
                'FEE_ROLE_INVALID');
            return;
        }
        if ($employee === '') {
            $this->REST_Return(422, 'employee_id wajib diisi.', array(), 'FEE_EMPLOYEE_REQUIRED');
            return;
        }
        if ($amount <= 0) {
            $this->REST_Return(422, 'amount harus lebih dari 0.', array(), 'FEE_AMOUNT_INVALID');
            return;
        }
        if ($idem === '') {
            $this->REST_Return(422, 'idempotency_key wajib diisi.', array(), 'IDEMPOTENCY_KEY_REQUIRED');
            return;
        }
        if (strlen($idem) > 64) {
            $this->REST_Return(422, 'idempotency_key maksimal 64 karakter.', array(), 'IDEMPOTENCY_KEY_INVALID');
            return;
        }

        // Dokter harus ada dan se-branch.
        $emp = $this->db->query("SELECT id, institute_branch_id FROM employee WHERE id = ?", array($employee))->row();
        if ($emp === null) {
            $this->REST_Return(404, 'Dokter/petugas tidak ditemukan.', array(), 'FEE_EMPLOYEE_NOT_FOUND');
            return;
        }
        if (trim((string) $emp->institute_branch_id) !== '' && (string) $emp->institute_branch_id !== $branch) {
            $this->_sg_audit('DENIED_FEE_EMPLOYEE_SCOPE_MISMATCH', $ok_id, $user_id,
                array('employee_id' => $employee, 'employee_branch' => $emp->institute_branch_id,
                      'token_branch' => $branch), 403);
            $this->REST_Return(403, 'Dokter/petugas berada di luar branch scope.', array(),
                'FEE_EMPLOYEE_SCOPE_MISMATCH');
            return;
        }

        $this->db->trans_start();
        // Replay: kunci idempotensi sama.
        $prev = $this->db->query(
            "SELECT * FROM operating_room_fee WHERE operating_room_id = ? AND idempotency_key = ?",
            array($ok_id, $idem))->row();
        if ($prev !== null) {
            if ((float) $prev->amount === round($amount, 2)) {
                $this->db->trans_commit();
                $this->REST_Return(200, 'SUCCESS (replay)', array(
                    'fee_id' => $prev->id, 'replayed' => true, 'amount' => (float) $prev->amount,
                ));
                return;
            }
            $this->_sg_deny('FEE_DUPLICATE_KEY', $ok_id, $user_id, 'fee_pay',
                array('idempotency_key' => $idem, 'existing_amount' => (float) $prev->amount,
                      'requested_amount' => $amount), 409,
                'Pembayaran dengan kunci ini sudah tercatat dengan nominal berbeda.');
            return;
        }
        // Guard Khanza: satu pembayaran per (operasi, role, dokter).
        $already = $this->db->query(
            "SELECT id, amount FROM operating_room_fee
              WHERE operating_room_id = ? AND role = ? AND employee_id = ? AND is_void = 0",
            array($ok_id, $role, $employee))->row();
        if ($already !== null) {
            $this->_sg_deny('FEE_ALREADY_PAID', $ok_id, $user_id, 'fee_pay',
                array('role' => $role, 'employee_id' => $employee,
                      'existing_fee_id' => $already->id, 'existing_amount' => (float) $already->amount), 409,
                'Jasa untuk role ini sudah dibayar.');
            return;
        }

        $fid = $this->_sg_uuid();
        $ok = $this->db->query(
            "INSERT INTO operating_room_fee (id, operating_room_id, institute_branch_id, role,
                employee_id, amount, paid_at, paid_by, idempotency_key, is_void)
             VALUES (?,?,?,?,?,?,?,?,?,0)",
            array($fid, $ok_id, $branch, $role, $employee, round($amount, 2),
                  date('Y-m-d H:i:s'), $user_id, $idem));
        if (!$ok) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menyimpan pembayaran jasa.', array(), 'FEE_WRITE_FAILED');
            return;
        }
        if (!$this->_sg_audit('SURGERY_FEE_PAID', $ok_id, $user_id, array(
            'fee_id' => $fid, 'role' => $role, 'employee_id' => $employee,
            'amount' => round($amount, 2), 'idempotency_key' => $idem,
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit pembayaran jasa.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }

        // Tahap 1: jurnal otomatis (debit piutang jasa medik / credit pendapatan jasa medik).
        $journal = $this->post_journal('SURGERY_FEE', $fid, 'SG-FEE-' . $fid, $branch, $user_id,
            array('receivable' => round($amount, 2), 'revenue' => round($amount, 2)),
            $role . '/' . $employee);
        if (isset($journal['status']) && $journal['status'] === 'error') {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Jurnal jasa medik gagal (tidak balance).', $journal, 'JOURNAL_FAILED');
            return;
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Pembayaran jasa gagal disimpan.', array(), 'FEE_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array('fee_id' => $fid, 'role' => $role,
            'employee_id' => $employee, 'amount' => round($amount, 2)));
        return;
    }

    /* ===================== report ===================== */
    public function report_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_sg_branch('report');
        if ($branch === null) { return; }

        $start = isset($q['date_start']) && $q['date_start'] !== '' ? $q['date_start'] : date('Y-m-01');
        $end   = isset($q['date_end']) && $q['date_end'] !== '' ? $q['date_end'] : date('Y-m-d');

        $rows = $this->db->query(
            "SELECT a.id, a.no_order, a.operation_date, a.status,
                    r.no_rawat, r.norm, p.fullname AS patient_name,
                    (SELECT COUNT(*) FROM operating_room_consumable c
                      WHERE c.operating_room_id = a.id AND c.is_delete = 0) AS consumable_count,
                    (SELECT IFNULL(SUM(c.total_price),0) FROM operating_room_consumable c
                      WHERE c.operating_room_id = a.id AND c.is_delete = 0) AS consumable_total,
                    (SELECT IFNULL(SUM(f.amount),0) FROM operating_room_fee f
                      WHERE f.operating_room_id = a.id AND f.is_void = 0) AS fee_total,
                    (SELECT COUNT(*) FROM operating_room_checklist k
                      WHERE k.operating_room_id = a.id AND k.is_delete = 0) AS checklist_count
               FROM operating_room a
               INNER JOIN registration_destination rd ON rd.id = a.registration_destination_id
               INNER JOIN registration r ON r.id = rd.registration_id
               INNER JOIN patient p ON p.norm = r.norm
              WHERE r.institute_branch_id = ? AND a.is_delete = 0
                AND DATE(a.operation_date) BETWEEN ? AND ?
              ORDER BY a.operation_date DESC",
            array($branch, $start, $end))->result();

        $list = array();
        $sum_consumable = 0.0;
        $sum_fee = 0.0;
        foreach ($rows as $r) {
            $sum_consumable += (float) $r->consumable_total;
            $sum_fee += (float) $r->fee_total;
            $list[] = array(
                'id' => $r->id, 'no_order' => $r->no_order, 'operation_date' => $r->operation_date,
                'status' => (int) $r->status, 'no_rawat' => $r->no_rawat, 'norm' => $r->norm,
                'patient_name' => $r->patient_name,
                'consumable_count' => (int) $r->consumable_count,
                'consumable_total' => (float) $r->consumable_total,
                'fee_total' => (float) $r->fee_total,
                'checklist_count' => (int) $r->checklist_count,
            );
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'period' => array('start' => $start, 'end' => $end),
            'branch' => $branch,
            'summary' => array(
                'operations' => count($list),
                'consumable_total' => round($sum_consumable, 2),
                'fee_total' => round($sum_fee, 2),
            ),
            'rows' => $list,
        ));
        return;
    }

    /* ===================== helpers ===================== */

    /** Branch HANYA dari token; kosong/tak valid => tolak (fail-closed). */
    protected function _sg_branch($operation) {
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
            $this->_sg_audit('DENIED_BRANCH_SCOPE_UNDEFINED', null,
                $this->security->xss_clean($t['data']->id),
                array('operation' => $operation, 'reason' => 'branch_empty'), 409);
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return null;
        }
        return $branch;
    }

    /** operating_room harus ada (404) dan se-branch (403). */
    protected function _sg_scope($operating_room_id, $branch, $operation) {
        if ($operating_room_id === null || $operating_room_id === '' || $operating_room_id === '0') {
            $this->REST_Return(422, 'operating_room_id wajib diisi.', array(), 'OPERATING_ROOM_ID_REQUIRED');
            return false;
        }
        $row = $this->db->query(
            "SELECT r.institute_branch_id
               FROM operating_room a
               INNER JOIN registration_destination rd ON rd.id = a.registration_destination_id
               INNER JOIN registration r ON r.id = rd.registration_id
              WHERE a.id = ?", array($operating_room_id))->row();
        if ($row === null) {
            $this->_sg_audit('DENIED_OPERATING_ROOM_NOT_FOUND', $operating_room_id, $this->_sg_user(),
                array('operation' => $operation), 404);
            $this->REST_Return(404, 'Operating room tidak ditemukan.', array(), 'OPERATING_ROOM_NOT_FOUND');
            return false;
        }
        $object_branch = trim((string) $row->institute_branch_id);
        if ($object_branch === '') {
            $this->_sg_audit('DENIED_OPERATING_ROOM_BRANCH_UNDEFINED', $operating_room_id, $this->_sg_user(),
                array('operation' => $operation), 409);
            $this->REST_Return(409, 'Kunjungan operasi tidak memiliki branch.', array(),
                'OPERATING_ROOM_BRANCH_UNDEFINED');
            return false;
        }
        if ($object_branch !== $branch) {
            $this->_sg_audit('DENIED_OPERATING_ROOM_SCOPE_MISMATCH', $operating_room_id, $this->_sg_user(),
                array('operation' => $operation, 'object_branch' => $object_branch, 'token_branch' => $branch), 403);
            $this->REST_Return(403, 'Data operasi berada di luar branch scope.', array(),
                'OPERATING_ROOM_SCOPE_MISMATCH');
            return false;
        }
        return true;
    }

    protected function _sg_deny($code, $ok_id, $user_id, $operation, $detail, $http, $message) {
        $this->db->trans_rollback();
        $this->_sg_audit('DENIED_' . $code, $ok_id, $user_id,
            array_merge(array('operation' => $operation, 'applied_by' => $user_id), (array) $detail), $http);
        $this->REST_Return($http, $message, (array) $detail, $code);
    }

    protected function _sg_user() {
        return isset($this->s1_actor['user_id']) ? $this->s1_actor['user_id'] : null;
    }

    /** Audit append-only ke log_activity. */
    protected function _sg_audit($result, $ok_id, $user_id, $detail = array(), $http = 200) {
        $audit_id = $this->_sg_uuid();
        $desc = json_encode(array_merge(array(
            'request_id'        => $this->debug_uniq,
            'endpoint'          => 'health_facility/surgery',
            'action'            => strtolower(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'),
            'result'            => $result,
            'http_status'       => $http,
            'operating_room_id' => $ok_id,
            'branch'            => isset($this->s1_actor['branch']) ? $this->s1_actor['branch'] : null,
        ), (array) $detail));
        $ok = $this->db->query(
            "INSERT INTO log_activity (id, noregistrasi, user_id, user_name, category_id, description,
                related_id, related_table, created_at) VALUES (?,?,?,?,?,?,?,?,?)",
            array($audit_id, null, $user_id,
                  isset($this->s1_actor['username']) ? $this->s1_actor['username'] : null,
                  0, $desc, $ok_id, 'operating_room', date('Y-m-d H:i:s')));
        if (!$ok) {
            Logger::error($this->debug_uniq, 'Surgery audit write failed: ' . $result);
        }
        return (bool) $ok;
    }

    protected function _sg_uuid() {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
    }

    /** Tahap 1: jurnal otomatis; 'skipped' diaudit, 'error' => pemanggil rollback. */
    protected function post_journal($type, $ref_id, $ref_no, $branch, $user_id, $amounts, $note = '') {
        $this->load->library('Journal');
        $res = $this->journal->post($type, $ref_id, $ref_no, $branch, $user_id, $amounts, null, (string) $note);
        if (isset($res['status']) && $res['status'] !== 'posted' && $res['status'] !== 'duplicate') {
            $this->_sg_audit('JOURNAL_' . strtoupper($res['status']), $ref_id, $user_id, $res, 200);
        }
        return $res;
    }
}
