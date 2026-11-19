<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;

/**
 * JURNAL — baca buku besar (E-6).
 *
 * Endpoint (read-only, GET):
 *   GET v1/finance/journal/list      daftar entry (filter tanggal, akun, ref_type, branch)
 *   GET v1/finance/journal/detail/:id  satu entry + baris debit/credit
 *   GET v1/finance/journal/balance   neraca saldo per akun (periode)
 *   GET v1/finance/journal/coa       daftar akun + saldo berjalan
 *
 * Branch SELALU dari token (fail-closed). Mode single cabang memakai branch default
 * lewat resolve_branch_id(), sama seperti modul lain.
 */
class Journal extends My_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Authorization_Token');
    }

    private function _branch($operation) {
        $t = $this->authorization_token->validateToken();
        if (empty($t) OR $t['status'] !== TRUE) {
            $this->REST_Return(401, isset($t['message']) ? $t['message'] : 'Invalid Token.', 'Invalid Token.');
            return null;
        }
        $branch = trim((string) $this->security->xss_clean($t['data']->institute_branch_id));
        // TODO: expand branch filter saat multi-cabang
        $branch = $this->resolve_branch_id($branch);
        if ($branch === '') {
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return null;
        }
        return $branch;
    }

    /* ===================== GET list ===================== */
    public function list_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_branch('list');
        if ($branch === null) { return; }

        $where  = "e.institute_branch_id = ?";
        $params = array($branch);
        if (isset($q['date_start']) && $q['date_start'] !== '') {
            $where .= " AND e.entry_date >= ?"; $params[] = $q['date_start'];
        }
        if (isset($q['date_end']) && $q['date_end'] !== '') {
            $where .= " AND e.entry_date <= ?"; $params[] = $q['date_end'];
        }
        if (isset($q['ref_type']) && $q['ref_type'] !== '') {
            $where .= " AND e.ref_type = ?"; $params[] = $q['ref_type'];
        }
        if (isset($q['status']) && $q['status'] !== '') {
            $where .= " AND e.status = ?"; $params[] = $q['status'];
        }
        if (isset($q['ref_no']) && $q['ref_no'] !== '') {
            $where .= " AND e.ref_no LIKE ?"; $params[] = '%' . $q['ref_no'] . '%';
        }
        // Filter akun: entry yang punya baris pada akun tsb.
        if (isset($q['account_code']) && $q['account_code'] !== '') {
            $where .= " AND EXISTS (SELECT 1 FROM journal_detail d WHERE d.entry_id = e.id AND d.account_code = ?)";
            $params[] = $q['account_code'];
        }
        $limit  = isset($q['limit']) ? max(1, min(500, (int) $q['limit'])) : 50;
        $offset = isset($q['offset']) ? max(0, (int) $q['offset']) : 0;

        $rows = $this->db->query(
            "SELECT e.id, e.entry_date, e.ref_no, e.ref_type, e.ref_id, e.description,
                    e.total_debit, e.total_credit, e.status, e.created_by, e.created_at,
                    (SELECT COUNT(*) FROM journal_detail d WHERE d.entry_id = e.id) AS line_count
               FROM journal_entry e
              WHERE $where
              ORDER BY e.entry_date DESC, e.created_at DESC
              LIMIT $limit OFFSET $offset", $params)->result();
        $total = $this->db->query("SELECT COUNT(*) AS n FROM journal_entry e WHERE $where", $params)->row();

        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'id'           => $r->id,
                'entry_date'   => $r->entry_date,
                'ref_no'       => $r->ref_no,
                'ref_type'     => $r->ref_type,
                'ref_id'       => $r->ref_id,
                'description'  => $r->description,
                'total_debit'  => (float) $r->total_debit,
                'total_credit' => (float) $r->total_credit,
                'status'       => $r->status,
                'line_count'   => (int) $r->line_count,
                'created_at'   => $r->created_at,
            );
        }
        $this->REST_Return(200, 'SUCCESS', (object) array(
            'rows' => $out, 'iTotal' => (int) $total->n, 'start' => $offset, 'branch' => $branch,
        ));
        return;
    }

    /* ===================== GET detail ===================== */
    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_branch('detail');
        if ($branch === null) { return; }

        // $p default 0 (bukan '') -> normalkan supaya "0" tidak dianggap id.
        $pkey = trim((string) $p);
        $id   = isset($q['id']) ? trim((string) $q['id']) : ($pkey === '0' ? '' : $pkey);
        if ($id === '') {
            $this->REST_Return(422, 'id jurnal wajib diisi.', array(), 'JOURNAL_ID_REQUIRED');
            return;
        }

        $e = $this->db->query(
            "SELECT * FROM journal_entry WHERE id = ? AND institute_branch_id = ?",
            array($id, $branch))->row();
        if ($e === null) {
            $this->REST_Return(404, 'Jurnal tidak ditemukan.', array(), 'JOURNAL_NOT_FOUND');
            return;
        }

        $lines = $this->db->query(
            "SELECT d.id, d.account_code, c.name AS account_name, d.debit, d.credit, d.description
               FROM journal_detail d
               LEFT JOIN b_coa c ON c.account = d.account_code
              WHERE d.entry_id = ?
              ORDER BY d.debit DESC, d.account_code", array($id))->result();

        $out = array();
        $td = 0.0; $tc = 0.0;
        foreach ($lines as $l) {
            $out[] = array(
                'account_code' => $l->account_code,
                'account_name' => $l->account_name,
                'debit'        => (float) $l->debit,
                'credit'       => (float) $l->credit,
                'description'  => $l->description,
            );
            $td += (float) $l->debit; $tc += (float) $l->credit;
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'id'           => $e->id,
            'entry_date'   => $e->entry_date,
            'ref_no'       => $e->ref_no,
            'ref_type'     => $e->ref_type,
            'ref_id'       => $e->ref_id,
            'description'  => $e->description,
            'status'       => $e->status,
            'created_at'   => $e->created_at,
            'total_debit'  => (float) $e->total_debit,
            'total_credit' => (float) $e->total_credit,
            'sum_debit'    => round($td, 2),
            'sum_credit'   => round($tc, 2),
            'balanced'     => abs($td - $tc) < 0.005,
            'lines'        => $out,
        ));
        return;
    }

    /* ===================== GET balance (neraca saldo) ===================== */
    public function balance_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'], $q);

        $branch = $this->_branch('balance');
        if ($branch === null) { return; }

        $where  = "e.institute_branch_id = ? AND e.status = 'posted'";
        $params = array($branch);
        if (isset($q['date_start']) && $q['date_start'] !== '') {
            $where .= " AND e.entry_date >= ?"; $params[] = $q['date_start'];
        }
        if (isset($q['date_end']) && $q['date_end'] !== '') {
            $where .= " AND e.entry_date <= ?"; $params[] = $q['date_end'];
        }

        $rows = $this->db->query(
            "SELECT d.account_code, c.name AS account_name,
                    IFNULL(SUM(d.debit),0)  AS debit,
                    IFNULL(SUM(d.credit),0) AS credit
               FROM journal_detail d
               INNER JOIN journal_entry e ON e.id = d.entry_id
               LEFT  JOIN b_coa c ON c.account = d.account_code
              WHERE $where
              GROUP BY d.account_code, c.name
              ORDER BY d.account_code", $params)->result();

        $out = array(); $td = 0.0; $tc = 0.0;
        foreach ($rows as $r) {
            $bal = (float) $r->debit - (float) $r->credit;
            $out[] = array(
                'account_code' => $r->account_code,
                'account_name' => $r->account_name,
                'debit'        => (float) $r->debit,
                'credit'       => (float) $r->credit,
                'balance'      => round($bal, 2),
            );
            $td += (float) $r->debit; $tc += (float) $r->credit;
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'branch'       => $branch,
            'date_start'   => isset($q['date_start']) ? $q['date_start'] : null,
            'date_end'     => isset($q['date_end']) ? $q['date_end'] : null,
            'accounts'     => $out,
            'total_debit'  => round($td, 2),
            'total_credit' => round($tc, 2),
            'balanced'     => abs($td - $tc) < 0.005,
        ));
        return;
    }

    /* ===================== GET coa ===================== */
    public function coa_get($p = 0) {
        header("Access-Control-Allow-Origin: *");

        $branch = $this->_branch('coa');
        if ($branch === null) { return; }

        $rows = $this->db->query(
            "SELECT c.account AS account_code, c.name AS account_name, c.type AS account_type,
                    IFNULL((SELECT SUM(d.debit - d.credit)
                              FROM journal_detail d
                              INNER JOIN journal_entry e ON e.id = d.entry_id
                             WHERE d.account_code = c.account AND e.status = 'posted'
                               AND e.institute_branch_id = ?), 0) AS balance
               FROM b_coa c
              WHERE IFNULL(c.is_delete,0) = 0
              GROUP BY c.account, c.name, c.type
              ORDER BY c.account", array($branch))->result();

        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'account_code' => $r->account_code,
                'account_name' => $r->account_name,
                'account_type' => $r->account_type,
                'balance'      => (float) $r->balance,
            );
        }
        $this->REST_Return(200, 'SUCCESS', (object) array('rows' => $out, 'branch' => $branch));
        return;
    }
}
