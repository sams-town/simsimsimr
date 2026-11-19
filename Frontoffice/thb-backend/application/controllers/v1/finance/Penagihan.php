<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * F3 PENAGIHAN PIUTANG — modul penagihan (dunning) di atas piutang pasien.
 *
 * Struktur mengikuti Khanza SIK:
 *   penagihan_piutang -> detail_penagihan_piutang -> bayar_piutang -> bukti_penagihan_piutang
 *
 * Endpoint (REST_Controller memetakan segmen URL ke <segmen>_<verb>):
 *   GET    v1/finance/penagihan/list
 *   GET    v1/finance/penagihan/detail/<no_tagihan>
 *   POST   v1/finance/penagihan/create
 *   PUT    v1/finance/penagihan/status/<no_tagihan>
 *   POST   v1/finance/penagihan/bayar/<no_tagihan>
 *   GET    v1/finance/penagihan/bukti/<id>
 *   DELETE v1/finance/penagihan/cancel/<no_tagihan>
 *
 * Aturan yang ditegakkan server-side:
 *   - branch HANYA dari token; kosong => default branch (mode single cabang)
 *   - no_tagihan wajib ada & se-branch => 404
 *   - total_tagihan = piutang.total_piutang (yang kini auto-calc dari baris invoice)
 *   - bayar <= sisa_tagihan; nota_bayar UNIQUE => idempoten (replay, bukan dobel)
 *   - tidak bisa bayar/ubah status bila status Lunas atau Nol
 *   - setiap aksi & penolakan diaudit (log_activity, related_table='penagihan_piutang')
 */
class Penagihan extends My_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
    }

    /* ===================== GET list ===================== */

    public function list_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '', $q);

        $branch = $this->_pg_branch('list');
        if ($branch === null) { return; }

        $where  = "pp.institute_branch_id = ?";
        $params = array($branch);
        foreach (array('status' => 'pp.status', 'no_tagihan' => 'pp.no_tagihan',
                       'no_rawat' => 'pp.no_rawat', 'no_invoice' => 'pp.no_invoice') as $k => $col) {
            if (isset($q[$k]) && $q[$k] !== '') { $where .= " AND $col = ?"; $params[] = $q[$k]; }
        }
        if (isset($q['nama_pasien']) && $q['nama_pasien'] !== '') {
            $where .= " AND pp.nama_pasien LIKE ?"; $params[] = '%' . $q['nama_pasien'] . '%';
        }
        if (isset($q['date_start']) && $q['date_start'] !== '') {
            $where .= " AND pp.tanggal_tagih >= ?"; $params[] = $q['date_start'];
        }
        if (isset($q['date_end']) && $q['date_end'] !== '') {
            $where .= " AND pp.tanggal_tagih <= ?"; $params[] = $q['date_end'];
        }

        $limit  = isset($q['limit'])  ? max(1, min(500, (int) $q['limit']))  : 50;
        $offset = isset($q['offset']) ? max(0, (int) $q['offset']) : 0;

        $total = $this->db->query(
            "SELECT COUNT(*) AS n FROM penagihan_piutang pp WHERE $where", $params)->row();
        $rows = $this->db->query(
            "SELECT pp.*,
                    IFNULL((SELECT SUM(b.jumlah_bayar + b.diskon) FROM bayar_piutang b
                             WHERE b.no_tagihan = pp.no_tagihan), 0) AS total_bayar,
                    (SELECT COUNT(*) FROM bayar_piutang b WHERE b.no_tagihan = pp.no_tagihan) AS jml_bayar
               FROM penagihan_piutang pp
              WHERE $where
              ORDER BY pp.tanggal_tagih DESC, pp.no_tagihan DESC
              LIMIT $limit OFFSET $offset", $params)->result();

        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'no_tagihan'    => $r->no_tagihan,
                'no_invoice'    => $r->no_invoice,
                'no_rawat'      => $r->no_rawat,
                'no_rkm_medis'  => $r->no_rkm_medis,
                'nama_pasien'   => $r->nama_pasien,
                'tanggal_tagih' => $r->tanggal_tagih,
                'tanggal_tempo' => $r->tanggal_tempo,
                'total_tagihan' => (float) $r->total_tagihan,
                'sisa_tagihan'  => (float) $r->sisa_tagihan,
                'total_bayar'   => (float) $r->total_bayar,
                'jml_bayar'     => (int) $r->jml_bayar,
                'status'        => $r->status,
                'nip_penagih'   => $r->nip_penagih,
            );
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'rows' => $out, 'total' => (int) ($total ? $total->n : 0),
            'limit' => $limit, 'offset' => $offset,
        ));
    }

    /* ===================== GET detail ===================== */

    public function detail_get($no_tagihan = '') {
        header("Access-Control-Allow-Origin: *");

        $branch = $this->_pg_branch('detail');
        if ($branch === null) { return; }

        $no_tagihan = $this->_pg_key($no_tagihan);
        $row = $this->_pg_find($no_tagihan, $branch);
        if ($row === null) {
            $this->REST_Return(404, 'Penagihan tidak ditemukan.', array(), 'PENAGIHAN_NOT_FOUND');
            return;
        }

        $lines = $this->db->query(
            "SELECT id, kd_rek, deskripsi, amount FROM detail_penagihan_piutang
              WHERE no_tagihan = ? ORDER BY id", array($no_tagihan))->result();
        $pays = $this->db->query(
            "SELECT id, nota_bayar, tanggal_bayar, jumlah_bayar, diskon, kd_rek_bayar,
                    nip_bayar, catatan, created_at, is_void, voided_by, voided_at,
                    void_reason, void_ref_no, approver_nip, approved_at
               FROM bayar_piutang WHERE no_tagihan = ? ORDER BY id", array($no_tagihan))->result();
        $bukti = $this->db->query(
            "SELECT id, no_bukti, tanggal_cetak, jumlah_total_dibayar, nip_pengaju
               FROM bukti_penagihan_piutang WHERE no_tagihan = ? ORDER BY id", array($no_tagihan))->result();

        $l = array(); $sum_bayar = 0.0;
        foreach ($lines as $x) {
            $l[] = array('id' => (int) $x->id, 'kd_rek' => $x->kd_rek,
                         'deskripsi' => $x->deskripsi, 'amount' => (float) $x->amount);
        }
        $p2 = array();
        foreach ($pays as $x) {
            // Hanya pembayaran sah yang dihitung: baris void tidak mengurangi tagihan.
            if ((int) $x->is_void === 0) {
                $sum_bayar += (float) $x->jumlah_bayar + (float) $x->diskon;
            }
            $p2[] = array('id' => (int) $x->id, 'bayar_id' => (int) $x->id,
                          'nota_bayar' => $x->nota_bayar,
                          'tanggal_bayar' => $x->tanggal_bayar,
                          'jumlah_bayar' => (float) $x->jumlah_bayar, 'diskon' => (float) $x->diskon,
                          'kd_rek_bayar' => $x->kd_rek_bayar, 'nip_bayar' => $x->nip_bayar,
                          'catatan' => $x->catatan, 'created_at' => $x->created_at,
                          'is_void' => (int) $x->is_void, 'voided_by' => $x->voided_by,
                          'voided_at' => $x->voided_at, 'void_reason' => $x->void_reason,
                          'void_ref_no' => $x->void_ref_no,
                          'approver_nip' => $x->approver_nip, 'approved_at' => $x->approved_at);
        }
        $b2 = array();
        foreach ($bukti as $x) {
            $b2[] = array('id' => (int) $x->id, 'no_bukti' => $x->no_bukti,
                          'tanggal_cetak' => $x->tanggal_cetak,
                          'jumlah_total_dibayar' => (float) $x->jumlah_total_dibayar,
                          'nip_pengaju' => $x->nip_pengaju);
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'no_tagihan'    => $row->no_tagihan,
            'piutang_id'    => $row->piutang_id,
            'no_invoice'    => $row->no_invoice,
            'no_rawat'      => $row->no_rawat,
            'no_rkm_medis'  => $row->no_rkm_medis,
            'nama_pasien'   => $row->nama_pasien,
            'tanggal_tagih' => $row->tanggal_tagih,
            'tanggal_tempo' => $row->tanggal_tempo,
            'total_tagihan' => (float) $row->total_tagihan,
            'sisa_tagihan'  => (float) $row->sisa_tagihan,
            'total_bayar'   => $sum_bayar,
            'status'        => $row->status,
            'insurance_id'  => (int) $row->insurance_id,
            'nip_penagih'   => $row->nip_penagih,
            'nip_setuju'    => $row->nip_setuju,
            'catatan'       => $row->catatan,
            'lines'         => $l,
            'payments'      => $p2,
            'bukti'         => $b2,
        ));
    }

    /* ===================== POST create ===================== */

    public function create_post($p = 0) {
        header("Access-Control-Allow-Origin: *");
        $data    = $this->json_post();
        $dt_post = isset($data['dt_postput']) ? $data['dt_postput'] : $data;

        $branch = $this->_pg_branch('create');
        if ($branch === null) { return; }
        $user_id = $this->_pg_user();

        $piutang_id = isset($dt_post['piutang_id']) ? trim((string) $dt_post['piutang_id']) : '';
        $no_rawat   = isset($dt_post['no_rawat']) ? trim((string) $dt_post['no_rawat']) : '';
        if ($piutang_id === '' && $no_rawat === '') {
            $this->REST_Return(422, 'piutang_id atau no_rawat wajib diisi.', array(), 'PENAGIHAN_KEY_REQUIRED');
            return;
        }

        $nip_penagih = isset($dt_post['nip_penagih']) ? trim((string) $dt_post['nip_penagih']) : '';
        $nip_setuju  = isset($dt_post['nip_setuju'])  ? trim((string) $dt_post['nip_setuju'])  : '';
        if ($nip_penagih === '' || $nip_setuju === '') {
            $this->REST_Return(422, 'nip_penagih dan nip_setuju wajib diisi.', array(),
                'PENAGIHAN_NIP_REQUIRED');
            return;
        }

        $this->db->trans_start();

        $where  = "p.institute_branch_id = ? AND p.is_delete = 0 AND p.status <> 'cancelled'";
        $params = array($branch);
        if ($piutang_id !== '') { $where .= " AND p.id = ?"; $params[] = $piutang_id; }
        else                    { $where .= " AND p.no_rawat = ?"; $params[] = $no_rawat; }

        $pj = $this->db->query(
            "SELECT p.* FROM piutang p WHERE $where ORDER BY p.created_at DESC LIMIT 1 FOR UPDATE",
            $params)->row();
        if ($pj === null) {
            $this->_pg_deny('PIUTANG_NOT_FOUND', null, $user_id, 'create',
                array('piutang_id' => $piutang_id, 'no_rawat' => $no_rawat), 404,
                'Piutang sumber tidak ditemukan.');
            return;
        }
        if ((float) $pj->total_piutang <= 0) {
            $this->_pg_deny('PIUTANG_TOTAL_ZERO', null, $user_id, 'create',
                array('piutang_id' => $pj->id), 400, 'Piutang tidak punya nilai tagihan.');
            return;
        }
        $dup = $this->db->query(
            "SELECT no_tagihan FROM penagihan_piutang WHERE no_invoice = ?",
            array($pj->invoice_no))->row();
        if ($dup !== null) {
            $this->_pg_deny('PENAGIHAN_ALREADY_EXISTS', $dup->no_tagihan, $user_id, 'create',
                array('no_invoice' => $pj->invoice_no, 'no_tagihan' => $dup->no_tagihan), 409,
                'Penagihan untuk invoice ini sudah ada.');
            return;
        }

        $no_tagihan = $this->_pg_tagihan_no();
        $tagih = isset($dt_post['tanggal_tagih']) && $dt_post['tanggal_tagih'] !== ''
            ? $dt_post['tanggal_tagih'] : date('Y-m-d');
        $tempo = isset($dt_post['tanggal_tempo']) && $dt_post['tanggal_tempo'] !== ''
            ? $dt_post['tanggal_tempo'] : date('Y-m-d', strtotime('+14 days'));
        $total = (float) $pj->total_piutang;
        $sisa  = (float) $pj->sisa_piutang;

        $ins = $this->db->query(
            "INSERT INTO penagihan_piutang
                (no_tagihan, piutang_id, no_invoice, no_rawat, no_rkm_medis, nama_pasien,
                 tanggal_tagih, tanggal_tempo, total_tagihan, sisa_tagihan, status,
                 insurance_id, company_id, nip_penagih, nip_setuju, catatan,
                 institute_branch_id, created_by)
             VALUES (?,?,?,?,?,?,?,?,?,?,'Open',?,?,?,?,?,?,?)",
            array($no_tagihan, $pj->id, $pj->invoice_no, $pj->no_rawat, $pj->norm, $pj->patient_name,
                  $tagih, $tempo, $total, $sisa,
                  isset($dt_post['insurance_id']) ? (int) $dt_post['insurance_id'] : (int) $pj->insurance_id,
                  $pj->company_id, $nip_penagih, $nip_setuju,
                  isset($dt_post['catatan']) ? trim((string) $dt_post['catatan']) : null,
                  $branch, $user_id));
        if (!$ins) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menyimpan penagihan.', array(), 'PENAGIHAN_CREATE_FAILED');
            return;
        }

        // Rincian tagihan = baris invoice piutang (sumber yang sama dengan total auto-calc).
        $items = $this->_pg_invoice_lines($pj->registration_id);
        if (!$items) {
            $this->db->trans_rollback();
            $this->REST_Return(400, 'Invoice lines kosong.', array(), 'INVOICE_LINES_EMPTY');
            return;
        }
        foreach ($items as $it) {
            $this->db->query(
                "INSERT INTO detail_penagihan_piutang (no_tagihan, kd_rek, deskripsi, amount)
                 VALUES (?,?,?,?)",
                array($no_tagihan, '1-1200', $it['name'] !== '' ? $it['name'] : 'Tagihan',
                      (float) $it['amount']));
        }

        if (!$this->_pg_audit('PENAGIHAN_CREATED', $no_tagihan, $user_id, array(
            'no_invoice' => $pj->invoice_no, 'no_rawat' => $pj->no_rawat,
            'total_tagihan' => $total, 'sisa_tagihan' => $sisa, 'lines' => count($items),
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit penagihan.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Penagihan gagal disimpan.', array(), 'PENAGIHAN_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'no_tagihan' => $no_tagihan, 'no_invoice' => $pj->invoice_no,
            'no_rawat' => $pj->no_rawat, 'total_tagihan' => $total,
            'sisa_tagihan' => $sisa, 'status' => 'Open', 'lines' => count($items),
        ));
    }

    /* ===================== PUT status ===================== */

    public function status_put($no_tagihan = '') {
        header("Access-Control-Allow-Origin: *");
        $data    = $this->json_post();
        $dt_post = isset($data['dt_postput']) ? $data['dt_postput'] : $data;

        $branch = $this->_pg_branch('status');
        if ($branch === null) { return; }
        $user_id = $this->_pg_user();

        $no_tagihan = $this->_pg_key($no_tagihan);
        $status = isset($dt_post['status']) ? trim((string) $dt_post['status']) : '';
        if (!in_array($status, array('Open', 'Proses Penagihan'), true)) {
            $this->REST_Return(422, 'status hanya boleh Open atau Proses Penagihan.', array(),
                'PENAGIHAN_STATUS_INVALID');
            return;
        }

        $this->db->trans_start();
        $row = $this->_pg_find($no_tagihan, $branch, true);
        if ($row === null) {
            $this->_pg_deny('PENAGIHAN_NOT_FOUND', $no_tagihan, $user_id, 'status', array(), 404,
                'Penagihan tidak ditemukan.');
            return;
        }
        if (in_array($row->status, array('Lunas', 'Nol'), true)) {
            $this->_pg_deny('PENAGIHAN_CLOSED', $no_tagihan, $user_id, 'status',
                array('status' => $row->status), 409, 'Penagihan sudah Lunas/Nol.');
            return;
        }
        $this->db->query("UPDATE penagihan_piutang SET status = ? WHERE no_tagihan = ?",
            array($status, $no_tagihan));
        $this->_pg_audit('PENAGIHAN_STATUS', $no_tagihan, $user_id,
            array('from' => $row->status, 'to' => $status));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Gagal ubah status.', array(), 'PENAGIHAN_STATUS_FAILED');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array('no_tagihan' => $no_tagihan, 'status' => $status));
    }

    /* ===================== POST bayar ===================== */

    public function bayar_post($no_tagihan = '') {
        header("Access-Control-Allow-Origin: *");
        $data    = $this->json_post();
        $dt_post = isset($data['dt_postput']) ? $data['dt_postput'] : $data;

        $branch = $this->_pg_branch('bayar');
        if ($branch === null) { return; }
        $user_id = $this->_pg_user();

        $no_tagihan = $this->_pg_key($no_tagihan);
        $amount   = isset($dt_post['amount']) ? round((float) $dt_post['amount'], 2) : 0.0;
        $discount = isset($dt_post['diskon']) ? round((float) $dt_post['diskon'], 2) : 0.0;
        $cara     = isset($dt_post['cara_bayar']) ? trim((string) $dt_post['cara_bayar']) : 'Tunai';
        if ($amount <= 0) {
            $this->REST_Return(422, 'amount harus lebih dari 0.', array(), 'PENAGIHAN_AMOUNT_INVALID');
            return;
        }
        if ($discount < 0) {
            $this->REST_Return(422, 'diskon tidak boleh negatif.', array(), 'PENAGIHAN_AMOUNT_INVALID');
            return;
        }

        $this->db->trans_start();
        $row = $this->_pg_find($no_tagihan, $branch, true);
        if ($row === null) {
            $this->_pg_deny('PENAGIHAN_NOT_FOUND', $no_tagihan, $user_id, 'bayar', array(), 404,
                'Penagihan tidak ditemukan.');
            return;
        }
        if (in_array($row->status, array('Lunas', 'Nol'), true)) {
            $this->_pg_deny('PENAGIHAN_CLOSED', $no_tagihan, $user_id, 'bayar',
                array('status' => $row->status), 409, 'Penagihan sudah Lunas/Nol.');
            return;
        }
        $sisa = (float) $row->sisa_tagihan;
        if ($amount + $discount > $sisa + 0.005) {
            $this->_pg_deny('PENAGIHAN_OVERPAID', $no_tagihan, $user_id, 'bayar',
                array('sisa' => $sisa, 'amount' => $amount, 'diskon' => $discount), 400,
                'Jumlah bayar melebihi sisa tagihan.');
            return;
        }

        // Idempotensi: nota_bayar UNIQUE. Replay => 200 dengan data yang sama, tidak dobel.
        $nota = isset($dt_post['nota_bayar']) ? trim((string) $dt_post['nota_bayar']) : '';
        if ($nota !== '') {
            $prev = $this->db->query(
                "SELECT * FROM bayar_piutang WHERE nota_bayar = ?", array($nota))->row();
            if ($prev !== null) {
                $this->db->trans_complete();
                $this->REST_Return(200, 'SUCCESS', array(
                    'replayed' => true, 'nota_bayar' => $prev->nota_bayar,
                    'no_tagihan' => $prev->no_tagihan,
                    'jumlah_bayar' => (float) $prev->jumlah_bayar,
                    'sisa_tagihan' => $sisa,
                ));
                return;
            }
        } else {
            $nota = $this->_pg_nota_no();
        }

        $rek = $this->_pg_cash_account($cara);
        if ($rek === null) {
            $this->_pg_deny('COA_MISSING', $no_tagihan, $user_id, 'bayar',
                array('cara_bayar' => $cara), 500, 'Akun kas untuk cara bayar tidak ada di COA.');
            return;
        }

        $ins = $this->db->query(
            "INSERT INTO bayar_piutang (no_tagihan, nota_bayar, tanggal_bayar, jumlah_bayar,
                diskon, kd_rek_bayar, kd_rek_diskon, nip_bayar, catatan, is_void)
             VALUES (?,?,?,?,?,?,?,?,?,0)",
            array($no_tagihan, $nota,
                  isset($dt_post['tanggal_bayar']) && $dt_post['tanggal_bayar'] !== ''
                      ? $dt_post['tanggal_bayar'] : date('Y-m-d'),
                  $amount, $discount, $rek,
                  $discount > 0 ? '5-1200' : null,
                  isset($dt_post['nip_bayar']) ? trim((string) $dt_post['nip_bayar']) : (string) $row->nip_penagih,
                  isset($dt_post['catatan']) ? trim((string) $dt_post['catatan']) : null));
        if (!$ins) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menyimpan pembayaran.', array(), 'PENAGIHAN_PAY_FAILED');
            return;
        }
        // Ambil insert_id SEGERA: query berikutnya (piutang_payment, bukti) menimpanya.
        $bayar_id = (int) $this->db->insert_id();

        // Catat juga di ledger piutang (satu sumber kebenaran untuk sisa piutang).
        // piutang_payment.idempotency_key UNIQUE => 'PG-' + nota_bayar.
        $pay_id = $this->_pg_uuid();
        $this->db->query(
            "INSERT INTO piutang_payment (id, piutang_id, amount, discount, cara_bayar, note,
                paid_at, paid_by, idempotency_key, is_void)
             VALUES (?,?,?,?,?,?,?,?,?,0)",
            array($pay_id, $row->piutang_id, $amount, $discount, $cara,
                  'Penagihan ' . $nota, date('Y-m-d H:i:s'), $user_id, 'PG-' . $nota));

        $agg = $this->db->query(
            "SELECT IFNULL(SUM(amount),0) AS dibayar, IFNULL(SUM(discount),0) AS diskon
               FROM piutang_payment WHERE piutang_id = ? AND IFNULL(is_void,0) = 0",
            array($row->piutang_id))->row();
        $dibayar = (float) $agg->dibayar;
        $diskon_all = (float) $agg->diskon;
        $sisa_pj = round((float) $row->total_tagihan - $dibayar - $diskon_all, 2);
        if ($sisa_pj < 0) { $sisa_pj = 0.0; }
        $st_pj = $sisa_pj <= 0 ? 'paid' : 'partial';
        $this->db->query(
            "UPDATE piutang SET total_dibayar = ?, sisa_piutang = ?, status = ?, invoice_status = ?,
                    updated_by = ?, updated_at = ?
              WHERE id = ?",
            array($dibayar, $sisa_pj, $st_pj, $st_pj, $user_id, date('Y-m-d H:i:s'), $row->piutang_id));

        $sum_tagihan = $this->db->query(
            "SELECT IFNULL(SUM(jumlah_bayar + diskon),0) AS n FROM bayar_piutang
              WHERE no_tagihan = ? AND IFNULL(is_void,0) = 0",
            array($no_tagihan))->row();
        $sisa_tagihan = round((float) $row->total_tagihan - (float) $sum_tagihan->n, 2);
        if ($sisa_tagihan < 0) { $sisa_tagihan = 0.0; }
        $status_baru = $sisa_tagihan <= 0 ? 'Lunas' : $row->status;
        $this->db->query(
            "UPDATE penagihan_piutang SET sisa_tagihan = ?, status = ? WHERE no_tagihan = ?",
            array($sisa_tagihan, $status_baru, $no_tagihan));

        // Bukti penagihan (kwitansi) per nota bayar.
        $no_bukti = 'BK-' . $nota;
        $this->db->query(
            "INSERT INTO bukti_penagihan_piutang (no_tagihan, no_bukti, tanggal_cetak,
                jumlah_total_dibayar, nip_pengaju) VALUES (?,?,?,?,?)",
            array($no_tagihan, $no_bukti, date('Y-m-d'), (float) $sum_tagihan->n,
                  isset($dt_post['nip_bayar']) ? trim((string) $dt_post['nip_bayar']) : (string) $row->nip_penagih));

        // Jurnal: debit kas/bank/EDC sesuai cara bayar, credit piutang (prinsip E-7).
        $journal = $this->post_journal('PENAGIHAN_PAYMENT', $no_tagihan, 'PG-' . $nota,
            $branch, $user_id,
            array('cash' => $cara === 'Tunai' ? $amount : 0,
                  'bank' => $cara === 'Transfer' ? $amount : 0,
                  'edc'  => ($cara !== 'Tunai' && $cara !== 'Transfer') ? $amount : 0,
                  // Debit kas + debit diskon harus sama dengan credit piutang.
                  'receivable' => $amount + $discount,
                  'discount' => $discount),
            $no_tagihan);
        if (isset($journal['status']) && $journal['status'] === 'error') {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Jurnal penagihan gagal (tidak balance).', $journal, 'JOURNAL_FAILED');
            return;
        }

        if (!$this->_pg_audit('PENAGIHAN_PAYMENT_OK', $no_tagihan, $user_id, array(
            'nota_bayar' => $nota, 'amount' => $amount, 'diskon' => $discount,
            'cara_bayar' => $cara, 'sisa_tagihan' => $sisa_tagihan, 'status' => $status_baru,
            'journal' => isset($journal['status']) ? $journal['status'] : 'unknown',
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit pembayaran.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Pembayaran gagal disimpan.', array(), 'PENAGIHAN_PAY_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'replayed' => false, 'no_tagihan' => $no_tagihan, 'nota_bayar' => $nota,
            'bayar_id' => $bayar_id,
            'no_bukti' => $no_bukti, 'jumlah_bayar' => $amount, 'diskon' => $discount,
            'sisa_tagihan' => $sisa_tagihan, 'status' => $status_baru,
            'piutang_sisa' => $sisa_pj, 'piutang_status' => $st_pj,
            'journal' => isset($journal['status']) ? $journal['status'] : 'unknown',
        ));
    }

    /* ===================== POST void (reverse) ===================== */

    /**
     * Void satu pembayaran penagihan + jurnal reverse.
     *
     * POST v1/finance/penagihan/void
     *   body { no_tagihan, bayar_id, reason, nip_void }
     *   atau ?no_tagihan=<TGH/...> di query string (no_tagihan memuat '/', jadi
     *   tidak bisa jadi segmen path CI).
     *
     * Arah jurnal KEBALIKAN dari E-7:
     *   debit  1-1200 Piutang Pasien   (piutang kembali)
     *   credit akun kas asli (kd_rek_bayar) + 5-1200 bila ada diskon
     */
    public function void_post($p = 0) {
        header("Access-Control-Allow-Origin: *");
        $data    = $this->json_post();
        $dt_post = isset($data['dt_postput']) ? $data['dt_postput'] : $data;

        $branch = $this->_pg_branch('void');
        if ($branch === null) { return; }
        $user_id = $this->_pg_user();

        $no_tagihan = $this->_pg_key(isset($dt_post['no_tagihan']) ? $dt_post['no_tagihan'] : '');
        $bayar_id   = isset($dt_post['bayar_id']) ? (int) $dt_post['bayar_id'] : 0;
        $reason     = isset($dt_post['reason']) ? trim((string) $dt_post['reason']) : '';
        $nip_void   = isset($dt_post['nip_void']) ? trim((string) $dt_post['nip_void']) : '';
        $approver   = isset($dt_post['approver_nip']) ? trim((string) $dt_post['approver_nip']) : '';

        if ($no_tagihan === '' || $bayar_id <= 0) {
            $this->REST_Return(400, 'no_tagihan dan bayar_id wajib diisi.', array(),
                'VALIDATION_ERROR');
            return;
        }
        if ($reason === '') {
            $this->REST_Return(400, 'reason wajib diisi.', array(), 'VALIDATION_ERROR');
            return;
        }
        if ($nip_void === '') {
            $nip_void = (string) $this->_pg_user_nip($user_id);
        }

        $this->db->trans_start();

        $row = $this->_pg_find($no_tagihan, $branch, true);
        if ($row === null) {
            $this->_pg_deny('PENAGIHAN_NOT_FOUND', $no_tagihan, $user_id, 'void', array(), 404,
                'Penagihan tidak ditemukan.');
            return;
        }
        $bay = $this->db->query(
            "SELECT * FROM bayar_piutang WHERE id = ? AND no_tagihan = ?",
            array($bayar_id, $no_tagihan))->row();
        if ($bay === null) {
            $this->_pg_deny('BAYAR_NOT_FOUND', $no_tagihan, $user_id, 'void',
                array('bayar_id' => $bayar_id), 404, 'Pembayaran tidak ditemukan pada tagihan ini.');
            return;
        }

        // Idempotensi: sudah void => 409 dengan record yang sama, TIDAK membuat jurnal baru.
        if ((int) $bay->is_void === 1) {
            $this->db->trans_complete();
            $this->REST_Return(409, 'Pembayaran sudah di-void.', array(
                'bayar_id'      => (int) $bay->id,
                'nota_bayar'    => $bay->nota_bayar,
                'is_void'       => 1,
                'voided_by'     => $bay->voided_by,
                'voided_at'     => $bay->voided_at,
                'void_reason'   => $bay->void_reason,
                'void_ref_no'   => $bay->void_ref_no,
                'approver_nip'  => isset($bay->approver_nip) ? $bay->approver_nip : null,
                'approved_at'   => isset($bay->approved_at) ? $bay->approved_at : null,
                'sisa_tagihan'  => (float) $row->sisa_tagihan,
            ), 'ALREADY_VOIDED');
            return;
        }
        // OPSI C: tagihan Lunas BOLEH di-void, tetapi wajib disetujui approver yang valid.
        // Tanpa approver -> 400 MISSING_APPROVER; approver tidak dikenal/nonaktif -> 400 INVALID_APPROVER.
        $need_approval = ($row->status === 'Lunas');
        if ($need_approval) {
            if ($approver === '') {
                $this->_pg_deny('MISSING_APPROVER', $no_tagihan, $user_id, 'void',
                    array('status' => $row->status, 'bayar_id' => $bayar_id), 400,
                    'Void pada tagihan Lunas wajib disertai approver_nip.');
                return;
            }
            if (!$this->_pg_approver_valid($approver)) {
                $this->_pg_deny('INVALID_APPROVER', $no_tagihan, $user_id, 'void',
                    array('approver_nip' => $approver), 400,
                    'approver_nip tidak ditemukan atau tidak aktif.');
                return;
            }
        }
        if ($row->status === 'Nol') {
            $this->_pg_deny('PENAGIHAN_CLOSED', $no_tagihan, $user_id, 'void',
                array('status' => $row->status), 409, 'Penagihan sudah dibatalkan (Nol).');
            return;
        }

        $amount   = (float) $bay->jumlah_bayar;
        $discount = (float) $bay->diskon;
        $rek      = trim((string) $bay->kd_rek_bayar);
        $void_ref = $this->_pg_void_no();

        $approved_at = $need_approval ? date('Y-m-d H:i:s') : null;
        $upd = $this->db->query(
            "UPDATE bayar_piutang
                SET is_void = 1, voided_by = ?, voided_at = ?, void_reason = ?, void_ref_no = ?,
                    approver_nip = ?, approved_at = ?
              WHERE id = ? AND no_tagihan = ? AND IFNULL(is_void,0) = 0",
            array($nip_void, date('Y-m-d H:i:s'), $reason, $void_ref,
                  $need_approval ? $approver : null, $approved_at,
                  $bayar_id, $no_tagihan));
        if (!$upd || $this->db->affected_rows() !== 1) {
            $this->db->trans_rollback();
            $this->REST_Return(409, 'Pembayaran sudah di-void (race).', array(), 'ALREADY_VOIDED');
            return;
        }

        // Jurnal reverse: debit piutang, credit kas asli.
        $journal = $this->post_journal('PENAGIHAN_VOID', $no_tagihan, $void_ref, $branch, $user_id,
            array('receivable' => $amount + $discount,
                  'cash'       => $amount,
                  'discount'   => $discount),
            'void ' . $bay->nota_bayar . ' oleh ' . $nip_void
                . ($need_approval ? ', disetujui ' . $approver : '') . ' — ' . $reason,
            array('cash' => $rek));
        if (isset($journal['status']) && $journal['status'] === 'error') {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Jurnal reverse gagal (tidak balance).', $journal, 'JOURNAL_FAILED');
            return;
        }

        // Recalculate: hanya pembayaran yang belum void.
        $agg = $this->db->query(
            "SELECT IFNULL(SUM(jumlah_bayar),0) AS bayar, IFNULL(SUM(diskon),0) AS diskon
               FROM bayar_piutang WHERE no_tagihan = ? AND IFNULL(is_void,0) = 0",
            array($no_tagihan))->row();
        $bayar_efektif = (float) $agg->bayar;
        $diskon_efektif = (float) $agg->diskon;
        $sisa = round((float) $row->total_tagihan - $bayar_efektif - $diskon_efektif, 2);
        if ($sisa < 0) { $sisa = 0.0; }
        $status_baru = $sisa <= 0 ? 'Lunas' : ($row->status === 'Proses Penagihan' ? 'Proses Penagihan' : 'Open');
        $this->db->query(
            "UPDATE penagihan_piutang SET sisa_tagihan = ?, status = ? WHERE no_tagihan = ?",
            array($sisa, $status_baru, $no_tagihan));

        // Void baris ledger piutang LEBIH DULU, supaya agregat di bawah tidak
        // menghitung pembayaran yang baru saja dibatalkan.
        $this->db->query(
            "UPDATE piutang_payment SET is_void = 1
              WHERE piutang_id = ? AND idempotency_key = ? AND IFNULL(is_void,0) = 0",
            array($row->piutang_id, 'PG-' . $bay->nota_bayar));

        // Piutang induk ikut dibuka kembali (sumber kebenaran sisa piutang).
        $aggp = $this->db->query(
            "SELECT IFNULL(SUM(amount),0) AS dibayar, IFNULL(SUM(discount),0) AS diskon
               FROM piutang_payment WHERE piutang_id = ? AND IFNULL(is_void,0) = 0",
            array($row->piutang_id))->row();
        $sisa_pj = round((float) $row->total_tagihan - (float) $aggp->dibayar - (float) $aggp->diskon, 2);
        if ($sisa_pj < 0) { $sisa_pj = 0.0; }
        $st_pj = $sisa_pj <= 0 ? 'paid' : ((float) $aggp->dibayar > 0 ? 'partial' : 'open');
        $this->db->query(
            "UPDATE piutang SET total_dibayar = ?, sisa_piutang = ?, status = ?, invoice_status = ?,
                    updated_by = ?, updated_at = ? WHERE id = ?",
            array((float) $aggp->dibayar, $sisa_pj, $st_pj, $st_pj, $user_id,
                  date('Y-m-d H:i:s'), $row->piutang_id));

        if (!$this->_pg_audit('PENAGIHAN_VOID_OK', $no_tagihan, $user_id, array(
            'bayar_id' => $bayar_id, 'nota_bayar' => $bay->nota_bayar,
            'amount' => $amount, 'diskon' => $discount, 'kd_rek_bayar' => $rek,
            'approver_nip' => $need_approval ? $approver : null,
            'approved_at'  => $approved_at,
            'reason' => $reason, 'nip_void' => $nip_void, 'void_ref_no' => $void_ref,
            'sisa_tagihan' => $sisa, 'status' => $status_baru,
            'journal' => isset($journal['status']) ? $journal['status'] : 'unknown',
        ))) {
            $this->db->trans_rollback();
            $this->REST_Return(500, 'Gagal menulis audit void.', array(), 'AUDIT_WRITE_FAILED');
            return;
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Void gagal disimpan.', array(), 'PENAGIHAN_VOID_ROLLBACK');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'bayar_id' => $bayar_id, 'nota_bayar' => $bay->nota_bayar,
            'is_void' => 1, 'voided_by' => $nip_void, 'void_ref_no' => $void_ref,
            'void_reason' => $reason, 'jumlah_bayar' => $amount, 'diskon' => $discount,
            'sisa_tagihan' => $sisa, 'status' => $status_baru,
            'piutang_sisa' => $sisa_pj, 'piutang_status' => $st_pj,
            'journal' => isset($journal['status']) ? $journal['status'] : 'unknown',
        ));
    }

    /* ===================== GET bayar list (termasuk void) ===================== */

    /**
     * GET v1/finance/penagihan/bayarlist?no_tagihan=<TGH/...>
     * Menampilkan semua pembayaran termasuk yang sudah void + flag.
     */
    public function bayarlist_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '', $q);

        $branch = $this->_pg_branch('bayarlist');
        if ($branch === null) { return; }

        $no_tagihan = $this->_pg_key(isset($q['no_tagihan']) ? $q['no_tagihan'] : '');
        if ($no_tagihan === '') {
            $this->REST_Return(400, 'no_tagihan wajib diisi.', array(), 'VALIDATION_ERROR');
            return;
        }
        $row = $this->_pg_find($no_tagihan, $branch);
        if ($row === null) {
            $this->REST_Return(404, 'Penagihan tidak ditemukan.', array(), 'PENAGIHAN_NOT_FOUND');
            return;
        }

        $rows = $this->db->query(
            "SELECT id, nota_bayar, tanggal_bayar, jumlah_bayar, diskon, kd_rek_bayar,
                    nip_bayar, catatan, created_at, is_void, voided_by, voided_at,
                    void_reason, void_ref_no, approver_nip, approved_at
               FROM bayar_piutang WHERE no_tagihan = ? ORDER BY id", array($no_tagihan))->result();

        $out = array(); $efektif = 0.0;
        foreach ($rows as $x) {
            if ((int) $x->is_void === 0) {
                $efektif += (float) $x->jumlah_bayar + (float) $x->diskon;
            }
            $out[] = array(
                'bayar_id'      => (int) $x->id,
                'nota_bayar'    => $x->nota_bayar,
                'tanggal_bayar' => $x->tanggal_bayar,
                'jumlah_bayar'  => (float) $x->jumlah_bayar,
                'diskon'        => (float) $x->diskon,
                'kd_rek_bayar'  => $x->kd_rek_bayar,
                'nip_bayar'     => $x->nip_bayar,
                'catatan'       => $x->catatan,
                'is_void'       => (int) $x->is_void,
                'voided_by'     => $x->voided_by,
                'voided_at'     => $x->voided_at,
                'void_reason'   => $x->void_reason,
                'void_ref_no'   => $x->void_ref_no,
                'approver_nip'  => $x->approver_nip,
                'approved_at'   => $x->approved_at,
            );
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'no_tagihan'    => $no_tagihan,
            'total_tagihan' => (float) $row->total_tagihan,
            'sisa_tagihan'  => (float) $row->sisa_tagihan,
            'status'        => $row->status,
            'dibayar_efektif' => round($efektif, 2),
            'rows'          => $out,
        ));
    }

    /* ===================== GET bukti ===================== */

    public function bukti_get($id = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '', $q);

        $branch = $this->_pg_branch('bukti');
        if ($branch === null) { return; }

        $id = (int) $id;
        if ($id > 0) {
            $row = $this->db->query(
                "SELECT b.*, pp.no_invoice, pp.no_rawat, pp.nama_pasien, pp.total_tagihan,
                        pp.sisa_tagihan, pp.status, pp.institute_branch_id
                   FROM bukti_penagihan_piutang b
                   JOIN penagihan_piutang pp ON pp.no_tagihan = b.no_tagihan
                  WHERE b.id = ? AND pp.institute_branch_id = ?", array($id, $branch))->row();
        } else {
            $row = $this->db->query(
                "SELECT b.*, pp.no_invoice, pp.no_rawat, pp.nama_pasien, pp.total_tagihan,
                        pp.sisa_tagihan, pp.status, pp.institute_branch_id
                   FROM bukti_penagihan_piutang b
                   JOIN penagihan_piutang pp ON pp.no_tagihan = b.no_tagihan
                  WHERE pp.institute_branch_id = ?
                  ORDER BY b.id DESC LIMIT 1", array($branch))->row();
        }
        if ($row === null) {
            $this->REST_Return(404, 'Bukti penagihan tidak ditemukan.', array(), 'BUKTI_NOT_FOUND');
            return;
        }

        $pays = $this->db->query(
            "SELECT nota_bayar, tanggal_bayar, jumlah_bayar, diskon, kd_rek_bayar
               FROM bayar_piutang WHERE no_tagihan = ? ORDER BY id", array($row->no_tagihan))->result();
        $p2 = array();
        foreach ($pays as $x) {
            $p2[] = array('nota_bayar' => $x->nota_bayar, 'tanggal_bayar' => $x->tanggal_bayar,
                          'jumlah_bayar' => (float) $x->jumlah_bayar, 'diskon' => (float) $x->diskon,
                          'kd_rek_bayar' => $x->kd_rek_bayar);
        }
        $this->REST_Return(200, 'SUCCESS', array(
            'id' => (int) $row->id, 'no_bukti' => $row->no_bukti,
            'no_tagihan' => $row->no_tagihan, 'no_invoice' => $row->no_invoice,
            'no_rawat' => $row->no_rawat, 'nama_pasien' => $row->nama_pasien,
            'tanggal_cetak' => $row->tanggal_cetak,
            'jumlah_total_dibayar' => (float) $row->jumlah_total_dibayar,
            'nip_pengaju' => $row->nip_pengaju,
            'total_tagihan' => (float) $row->total_tagihan,
            'sisa_tagihan' => (float) $row->sisa_tagihan,
            'status' => $row->status, 'payments' => $p2,
        ));
    }

    /* ===================== DELETE cancel ===================== */

    public function cancel_post($no_tagihan = '') {
        header("Access-Control-Allow-Origin: *");

        $branch = $this->_pg_branch('cancel');
        if ($branch === null) { return; }
        $user_id = $this->_pg_user();

        $no_tagihan = $this->_pg_key($no_tagihan);

        $this->db->trans_start();
        $row = $this->_pg_find($no_tagihan, $branch, true);
        if ($row === null) {
            $this->_pg_deny('PENAGIHAN_NOT_FOUND', $no_tagihan, $user_id, 'cancel', array(), 404,
                'Penagihan tidak ditemukan.');
            return;
        }
        if ($row->status === 'Nol') {
            $this->_pg_deny('PENAGIHAN_ALREADY_NOL', $no_tagihan, $user_id, 'cancel', array(), 409,
                'Penagihan sudah dibatalkan.');
            return;
        }
        // Fail-closed: tagihan yang sudah menerima uang tidak boleh dibatalkan diam-diam.
        $n = $this->db->query("SELECT COUNT(*) AS n FROM bayar_piutang WHERE no_tagihan = ?",
            array($no_tagihan))->row();
        if ($n !== null && (int) $n->n > 0) {
            $this->_pg_deny('PENAGIHAN_HAS_PAYMENT', $no_tagihan, $user_id, 'cancel',
                array('payments' => (int) $n->n), 409,
                'Penagihan sudah punya pembayaran; batalkan lewat void pembayaran.');
            return;
        }

        $this->db->query("UPDATE penagihan_piutang SET status = 'Nol' WHERE no_tagihan = ?",
            array($no_tagihan));
        $this->_pg_audit('PENAGIHAN_CANCELLED', $no_tagihan, $user_id,
            array('from' => $row->status, 'to' => 'Nol'));
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->REST_Return(422, 'Gagal membatalkan penagihan.', array(), 'PENAGIHAN_CANCEL_FAILED');
            return;
        }
        $this->REST_Return(200, 'SUCCESS', array('no_tagihan' => $no_tagihan, 'status' => 'Nol'));
    }

    /* ===================== helpers ===================== */

    /** Baris invoice piutang (sumber yang sama dengan auto-calc total di Piutang::create). */
    protected function _pg_invoice_lines($registration_id) {
        $inv = $this->db->query(
            "SELECT id FROM transaction_invoice
              WHERE registration_id = ? AND IFNULL(is_delete,0) = 0
              ORDER BY date_added DESC LIMIT 1", array($registration_id))->row();
        if ($inv === null) { return array(); }
        $rows = $this->db->query(
            "SELECT COALESCE(li.costing_name, ri.costing_name, ta.costing_name, td.costing_name, '') AS item_name,
                    COALESCE(li.var_1, ri.var_1, ta.var_1, td.var_1, 0) AS amount
               FROM transaction_invoice_item tii
               LEFT JOIN laboratory_request_item li ON li.id = tii.transaction_id AND tii.type_transaction_id = 12
               LEFT JOIN radiology_request_item  ri ON ri.id = tii.transaction_id AND tii.type_transaction_id = 15
               LEFT JOIN transaction_administration ta ON ta.id = tii.transaction_id AND tii.type_transaction_id = 11
               LEFT JOIN transaction_doctor       td ON td.id = tii.transaction_id AND tii.type_transaction_id IN (1,2)
              WHERE tii.transaction_invoice_id = ? AND IFNULL(tii.is_delete,0) = 0",
            array($inv->id))->result();
        $out = array();
        foreach ($rows as $r) {
            $out[] = array('name' => (string) $r->item_name, 'amount' => (float) $r->amount);
        }
        return $out;
    }

    /** Format no_tagihan: TGH/MM/YYYY/NNNN, urut per bulan, server-side. */
    protected function _pg_tagihan_no() {
        $prefix = 'TGH/' . date('m') . '/' . date('Y') . '/';
        $row = $this->db->query(
            "SELECT no_tagihan FROM penagihan_piutang
              WHERE no_tagihan LIKE ? ORDER BY no_tagihan DESC LIMIT 1",
            array($prefix . '%'))->row();
        $seq = 1;
        if ($row !== null) {
            $seq = (int) substr((string) $row->no_tagihan, strlen($prefix)) + 1;
        }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /** Format no_jurnal reverse: RV-YYYY/MM/DD/NNNN (urut per hari). */
    /**
     * Approver sah = terdaftar di employee_master (aktif) ATAU di tabel operasional employee.
     * employee_master adalah hasil import master (bisa masih kosong); employee adalah data FO
     * yang dipakai aplikasi sehari-hari, jadi keduanya diterima.
     */
    protected function _pg_approver_valid($nip) {
        $nip = trim((string) $nip);
        if ($nip === '') {
            return false;
        }
        $a = $this->db->query(
            "SELECT 1 AS ok FROM employee_master WHERE employee_nip = ? AND is_active = 1 LIMIT 1",
            array($nip))->row();
        if ($a !== null) {
            return true;
        }
        $b = $this->db->query(
            "SELECT 1 AS ok FROM employee WHERE employee_id = ? AND IFNULL(is_delete,0) = 0 LIMIT 1",
            array($nip))->row();
        return $b !== null;
    }

    protected function _pg_void_no() {
        $prefix = 'RV-' . date('Y/m/d') . '/';
        $row = $this->db->query(
            "SELECT ref_no FROM journal_entry WHERE ref_no LIKE ? ORDER BY ref_no DESC LIMIT 1",
            array($prefix . '%'))->row();
        $seq = 1;
        if ($row !== null) { $seq = (int) substr((string) $row->ref_no, strlen($prefix)) + 1; }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /** NIP/nama aktor untuk jejak void (fallback ke username bila employee tidak ada). */
    protected function _pg_user_nip($user_id) {
        $u = isset($this->s1_actor['username']) ? (string) $this->s1_actor['username'] : '';
        if ($user_id !== null && $user_id !== '') {
            // employee tidak punya kolom `nip` di FO; dipakai employee_id sebagai identitas kerja.
            $r = $this->db->query(
                "SELECT e.employee_id FROM user u JOIN employee e ON e.id = u.employee_id
                  WHERE u.id = ? LIMIT 1", array($user_id))->row();
            if ($r !== null && trim((string) $r->employee_id) !== '') {
                return trim((string) $r->employee_id);
            }
        }
        return $u !== '' ? $u : 'SYSTEM';
    }

    /** Format nota_bayar: NB/MM/YYYY/NNNN. */
    protected function _pg_nota_no() {
        $prefix = 'NB/' . date('m') . '/' . date('Y') . '/';
        $row = $this->db->query(
            "SELECT nota_bayar FROM bayar_piutang WHERE nota_bayar LIKE ?
              ORDER BY nota_bayar DESC LIMIT 1", array($prefix . '%'))->row();
        $seq = 1;
        if ($row !== null) { $seq = (int) substr((string) $row->nota_bayar, strlen($prefix)) + 1; }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /** Akun kas sesuai cara bayar (dari b_coa; fallback mapping config). */
    protected function _pg_cash_account($cara) {
        $this->config->load('coa_mapping', true);
        $map = $this->config->item('coa_mapping');
        $acc = '1-1100';
        if ($cara === 'Transfer') { $acc = '1-1110'; }
        elseif ($cara !== 'Tunai') { $acc = '1-1120'; }
        $ok = $this->db->query("SELECT account FROM b_coa WHERE account = ? AND IFNULL(is_delete,0)=0",
            array($acc))->row();
        return $ok === null ? null : $acc;
    }

    /**
     * no_tagihan berformat TGH/MM/YYYY/NNNN sehingga '/' memecah path CI.
     * Terima dari segmen path ATAU ?no_tagihan= (dipakai FE/harness).
     */
    protected function _pg_key($p) {
        $k = trim((string) $p);
        if ($k === '' || $k === '0') {
            $q = array();
            parse_str(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '', $q);
            $k = isset($q['no_tagihan']) ? trim((string) $q['no_tagihan']) : $k;
        }
        return $k;
    }

    protected function _pg_find($no_tagihan, $branch, $for_update = false) {
        if ($no_tagihan === '') { return null; }
        // MariaDB: FOR UPDATE harus SETELAH LIMIT, bukan sebelumnya.
        $sql = "SELECT * FROM penagihan_piutang
                 WHERE no_tagihan = ? AND institute_branch_id = ?
                 LIMIT 1" . ($for_update ? " FOR UPDATE" : "");
        return $this->db->query($sql, array($no_tagihan, $branch))->row();
    }

    /** Branch dari token; kosong => default branch (mode single cabang). */
    protected function _pg_branch($operation) {
        $t = $this->authorization_token->validateToken();
        if (empty($t) OR $t['status'] !== TRUE) {
            $this->REST_Return(401, isset($t['message']) ? $t['message'] : 'Invalid Token.', 'Invalid Token.');
            return null;
        }
        $branch = trim((string) $this->security->xss_clean($t['data']->institute_branch_id));
        $branch = $this->resolve_branch_id($branch);
        if ($branch === '') {
            $this->_pg_audit('DENIED_BRANCH_SCOPE_UNDEFINED', null,
                $this->security->xss_clean($t['data']->id),
                array('reason' => 'branch_empty', 'operation' => $operation), 409);
            $this->REST_Return(409, 'Branch scope tidak terdefinisi.', array(), 'BRANCH_SCOPE_UNDEFINED');
            return null;
        }
        return $branch;
    }

    protected function _pg_user() {
        return isset($this->s1_actor['user_id']) ? $this->s1_actor['user_id'] : null;
    }

    protected function _pg_deny($code, $no_tagihan, $user_id, $operation, $detail, $http, $message) {
        $this->db->trans_rollback();
        $this->_pg_audit('DENIED_' . $code, $no_tagihan, $user_id,
            array_merge(array('operation' => $operation,
                              'applied_by' => $user_id,
                              'applied_from_branch' => isset($this->s1_actor['branch']) ? $this->s1_actor['branch'] : null),
                        (array) $detail), $http);
        $this->REST_Return($http, $message, (array) $detail, $code);
    }

    protected function _pg_audit($result, $no_tagihan, $user_id, $detail = array(), $http = 200) {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        $audit_id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
        $desc = json_encode(array_merge(array(
            'request_id' => $this->debug_uniq,
            'endpoint'   => 'finance/penagihan',
            'action'     => strtolower(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'),
            'result'     => $result,
            'http_status'=> $http,
            'no_tagihan' => $no_tagihan,
            'branch'     => isset($this->s1_actor['branch']) ? $this->s1_actor['branch'] : null,
        ), (array) $detail));
        $ok = $this->db->query(
            "INSERT INTO log_activity (id, noregistrasi, user_id, user_name, category_id, description,
                related_id, related_table, created_at) VALUES (?,?,?,?,?,?,?,?,?)",
            array($audit_id, null, $user_id,
                  isset($this->s1_actor['username']) ? $this->s1_actor['username'] : null,
                  0, $desc, $no_tagihan, 'penagihan_piutang', date('Y-m-d H:i:s')));
        if (!$ok) { Logger::error($this->debug_uniq, 'Penagihan audit write failed: ' . $result); }
        return (bool) $ok;
    }

    protected function _pg_uuid() {
        $b = random_bytes(16);
        $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
        $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
    }

    protected function post_journal($type, $ref_id, $ref_no, $branch, $user_id, $amounts, $note = '',
                                    $account_override = array()) {
        $this->load->library('Journal');
        $res = $this->journal->post($type, $ref_id, $ref_no, $branch, $user_id, $amounts, null,
                                    (string) $note, (array) $account_override);
        if (isset($res['status']) && $res['status'] !== 'posted') {
            $this->_pg_audit('JOURNAL_' . strtoupper($res['status']), $ref_id, $user_id,
                array('ref_no' => $ref_no, 'journal' => $res), 200);
        }
        return $res;
    }
}
