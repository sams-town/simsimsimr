<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Journal — jurnal otomatis double-entry (tahap 1 deployment readiness).
 *
 * Prinsip:
 *   - total_debit == total_credit WAJIB. Kalau tidak balance, tidak ada yang ditulis.
 *   - idempotent: UNIQUE (institute_branch_id, ref_no). Replay mengembalikan baris lama.
 *   - akun diambil dari config/coa_mapping.php, BUKAN dari payload client.
 *   - kalau mapping/akun belum ada, jurnal DI-SKIP (transaksi bisnis tetap jalan)
 *     dan alasannya dikembalikan supaya pemanggil bisa audit.
 *
 * Tidak pernah menulis ke b_coa (COA adalah master, bukan hasil transaksi).
 */
class Journal {

    private $CI;
    private $map = array();
    private $tolerance = 0.005;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('coa_mapping', TRUE);
        $m = $this->CI->config->item('coa_mapping', 'coa_mapping');
        $this->map = is_array($m) ? $m : array();
    }

    /**
     * @param string $type    kunci mapping (PIUTANG_CREATE, PIUTANG_PAYMENT, SURGERY_FEE, PHARMACY_DISPENSE)
     * @param string $ref_id  id objek bisnis (piutang_id / payment_id / fee_id / prescription_id)
     * @param string $ref_no  kunci idempotensi jurnal (harus unik per branch)
     * @param string $branch  institute_branch_id
     * @param string $user_id aktor
     * @param array  $amounts role => nilai (lihat config)
     * @param string $date    Y-m-d (default hari ini)
     * @param string $desc    keterangan tambahan
     * @return array status: posted | duplicate | skipped | error
     */
    public function post($type, $ref_id, $ref_no, $branch, $user_id, $amounts, $date = null, $desc = '',
                         $account_override = array()) {
        if (!isset($this->map[$type])) {
            return $this->skip('MAPPING_MISSING', $type);
        }
        if (trim((string) $branch) === '') {
            return $this->skip('BRANCH_UNDEFINED', $type);
        }
        $date = $date ?: date('Y-m-d');

        // Idempotensi: ref_no sama di branch sama => jangan dobel.
        $prev = $this->CI->db->query(
            "SELECT id FROM journal_entry WHERE institute_branch_id = ? AND ref_no = ?",
            array($branch, $ref_no))->row();
        if ($prev !== null) {
            return array('status' => 'duplicate', 'entry_id' => $prev->id, 'ref_no' => $ref_no);
        }

        // Susun baris; nilai 0 dilewati. Akun harus ada di b_coa.
        $lines = array();
        $total_debit = 0.0;
        $total_credit = 0.0;
        foreach ($this->map[$type]['lines'] as $l) {
            $role = $l['role'];
            $val  = isset($amounts[$role]) ? round((float) $amounts[$role], 2) : 0.0;
            if ($val <= 0) {
                continue;
            }
            // Akun boleh ditimpa pemanggil (mis. kas vs bank vs EDC pada pembayaran kasir).
            $acc = isset($account_override[$role])
                ? trim((string) $account_override[$role])
                : trim((string) $l['account']);
            $exists = $this->CI->db->query(
                "SELECT account FROM b_coa WHERE account = ? AND IFNULL(is_delete,0) = 0 LIMIT 1",
                array($acc))->row();
            if ($exists === null) {
                return $this->skip('ACCOUNT_MISSING', $type, array('account' => $acc));
            }
            $lines[] = array(
                'account' => $acc,
                'debit'   => ($l['side'] === 'debit') ? $val : 0.0,
                'credit'  => ($l['side'] === 'credit') ? $val : 0.0,
                'desc'    => $this->map[$type]['description'] . ($desc !== '' ? ' — ' . $desc : ''),
            );
            if ($l['side'] === 'debit') { $total_debit += $val; } else { $total_credit += $val; }
        }

        if (!$lines) {
            return $this->skip('ZERO_AMOUNT', $type);
        }
        // Double-entry: tidak balance => tidak ditulis sama sekali.
        if (abs($total_debit - $total_credit) > $this->tolerance) {
            return array('status' => 'error', 'reason' => 'UNBALANCED',
                         'total_debit' => $total_debit, 'total_credit' => $total_credit);
        }

        $entry_id = $this->uuid();
        $ok = $this->CI->db->query(
            "INSERT INTO journal_entry (id, institute_branch_id, entry_date, ref_no, ref_type, ref_id,
                description, total_debit, total_credit, status, created_by, created_at)
             VALUES (?,?,?,?,?,?,?,?,?,'posted',?,?)",
            array($entry_id, $branch, $date, $ref_no, $type, $ref_id,
                  $this->map[$type]['description'] . ($desc !== '' ? ' — ' . $desc : ''),
                  round($total_debit, 2), round($total_credit, 2), $user_id, date('Y-m-d H:i:s')));
        if (!$ok) {
            return array('status' => 'error', 'reason' => 'ENTRY_WRITE_FAILED');
        }

        foreach ($lines as $l) {
            $ok = $this->CI->db->query(
                "INSERT INTO journal_detail (id, entry_id, account_code, debit, credit, description)
                 VALUES (?,?,?,?,?,?)",
                array($this->uuid(), $entry_id, $l['account'], $l['debit'], $l['credit'], $l['desc']));
            if (!$ok) {
                return array('status' => 'error', 'reason' => 'DETAIL_WRITE_FAILED', 'entry_id' => $entry_id);
            }
        }

        // Verifikasi ulang dari DB (bukan dari variabel) sebelum dianggap sukses.
        $chk = $this->CI->db->query(
            "SELECT IFNULL(SUM(debit),0) d, IFNULL(SUM(credit),0) c, COUNT(*) n
               FROM journal_detail WHERE entry_id = ?", array($entry_id))->row();
        if ($chk === null || abs((float) $chk->d - (float) $chk->c) > $this->tolerance) {
            return array('status' => 'error', 'reason' => 'UNBALANCED_AFTER_WRITE', 'entry_id' => $entry_id);
        }

        return array('status' => 'posted', 'entry_id' => $entry_id, 'ref_no' => $ref_no,
                     'lines' => count($lines), 'total' => round($total_debit, 2));
    }

    private function skip($reason, $type, $extra = array()) {
        return array_merge(array('status' => 'skipped', 'reason' => $reason, 'type' => $type), $extra);
    }

    private function uuid() {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
    }
}
