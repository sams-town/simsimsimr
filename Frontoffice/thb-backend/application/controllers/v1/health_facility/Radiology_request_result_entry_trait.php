<?php
/**
 * Hasil radiologi — entry per ORDER (mirror Laboratory_request_result_entry_trait).
 *
 * Trait dipakai controllers/v1/health_facility/Radiology_request.php.
 *
 * ENDPOINT
 *   GET  version/health_facility/radiology_request/result_entry/(:any)        -> order id
 *   PUT  version/health_facility/radiology_request/result_entry_order/(:any)  -> order id
 *        body: { "items": [ { "radiology_request_item_id": "<item id>",
 *                             "result": "<bacaan/narasi>", "url": "<path/gambar>",
 *                             "notes": "..." } ],
 *                "conclusion": "", "notes": "", "advice": "" }
 *
 * ATURAN (sama dengan sisi laboratorium)
 *   - Semua item harus milik order di path; item asing -> 422, tidak ada yang ditulis.
 *   - Satu transaksi; satu baris gagal = seluruh batch rollback.
 *   - radiology_request.id dan radiology_request_item.id tidak pernah dipertukarkan.
 *   - Order ditandai status=3 + date_result hanya bila SEMUA item non-deleted
 *     sudah punya date_result.
 *
 * Kolom yang dipakai (sudah ada di schema, tanpa ALTER):
 *   radiology_request_item        : notes, url, status, date_result
 *   radiology_request             : status, date_result, user_lab_id, conclusion, notes, advice
 *   radiology_request_item_image  : url, conclusion, notes, advice, user_lab_id, date_result, status
 */
trait Radiology_request_result_entry_trait
{
    /** GET result_entry/(:any) */
    public function result_entry_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");

        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) OR $is_valid_token['status'] !== TRUE) {
            $this->REST_Return(401, 'Invalid Token.', 'Invalid Token.');
            return;
        }

        $id = trim((string) $p);
        if ($id === '' || $id === '0') {
            $this->REST_Return(422, 'Format error', array('id' => 'id is required'));
            return;
        }
        $id = $this->security->xss_clean($id);

        $request = $this->Radiology_request->read($id);
        if (!$request) {
            $this->REST_Return(404, 'Radiology_request with id ' . $id . ' does not exist!');
            return;
        }

        $items = $this->load_rad_order_items($id);
        if (!$items) {
            $this->REST_Return(404, 'Radiology_request with id ' . $id . ' has no item!');
            return;
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'id'           => $id,
            'no_request'   => isset($request[0]->no_request) ? $request[0]->no_request : null,
            'status'       => isset($request[0]->status) ? (int) $request[0]->status : null,
            'date_request' => isset($request[0]->date_request) ? $request[0]->date_request : null,
            'conclusion'   => isset($request[0]->conclusion) ? $request[0]->conclusion : null,
            'notes'        => isset($request[0]->notes) ? $request[0]->notes : null,
            'advice'       => isset($request[0]->advice) ? $request[0]->advice : null,
            'items'        => $this->build_rad_result_entry($items),
        ));
        return;
    }

    /** PUT result_entry_order/(:any) */
    public function result_entry_order_put($p = 0)
    {
        $data   = $this->json_put();
        $dt_put = $data['dt_postput'];

        $is_valid_token = $this->authorization_token->validateToken();
        if (empty($is_valid_token) OR $is_valid_token['status'] !== TRUE) {
            $this->REST_Return(401, 'Invalid Token.', 'Invalid Token.');
            return;
        }
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $id = trim((string) $p);
        if ($id === '' || $id === '0') {
            $id = isset($dt_put['id']) ? trim((string) $dt_put['id']) : '';
        }
        $this->form_validation->set_data(array('id' => $id));
        $this->form_validation->set_rules('id', 'id', 'trim|required|max_length[36]');
        if ($this->form_validation->run() == FALSE) {
            $this->REST_Return(404, 'Radiology_request with id ' . $id . ' does not exist!');
            return;
        }
        $id = $this->security->xss_clean($id);

        $request = $this->Radiology_request->read($id);
        if (!$request) {
            $this->REST_Return(404, 'Radiology_request with id ' . $id . ' does not exist!');
            return;
        }
        $items = $this->load_rad_order_items($id);
        if (!$items) {
            $this->REST_Return(404, 'Radiology_request with id ' . $id . ' has no item!');
            return;
        }

        $payload_items = isset($dt_put['items']) ? $dt_put['items'] : null;
        if (!is_array($payload_items) OR count($payload_items) === 0) {
            $this->REST_Return(422, 'Format error', array('items' => 'items must be a non-empty array.'));
            return;
        }

        // --- Pre-flight: ownership + shape. Tidak ada yang ditulis sebelum ini lolos. ---
        $owned = array();
        foreach ($items as $it) { $owned[(string) $it->id] = true; }

        $plan   = array();
        $errors = array();
        foreach ($payload_items as $idx => $entry) {
            $item_id = isset($entry['radiology_request_item_id']) ? trim((string) $entry['radiology_request_item_id']) : '';
            if ($item_id === '') {
                $errors[] = array('index' => $idx, 'field' => 'radiology_request_item_id', 'reason' => 'required');
                continue;
            }
            if (!isset($owned[$item_id])) {
                $errors[] = array('index' => $idx, 'field' => 'radiology_request_item_id',
                    'reason' => 'item ' . $item_id . ' does not belong to radiology_request ' . $id);
                continue;
            }
            $result = isset($entry['result']) ? trim((string) $entry['result']) : '';
            $url    = isset($entry['url']) ? trim((string) $entry['url']) : '';
            if ($result === '' && $url === '') {
                $errors[] = array('index' => $idx, 'field' => 'result',
                    'reason' => 'result atau url wajib diisi', 'radiology_request_item_id' => $item_id);
                continue;
            }
            $plan[$item_id] = array(
                'result' => $result === '' ? null : $this->security->xss_clean($result),
                'url'    => $url === '' ? null : $this->security->xss_clean($url),
                'notes'  => isset($entry['notes']) ? $this->security->xss_clean((string) $entry['notes']) : null,
            );
        }
        if (count($errors) > 0) {
            $this->REST_Return(422, 'Required fields are missing or invalid. Nothing was saved.', array(
                'id' => $id, 'errors' => $errors, 'saved' => 0,
            ));
            return;
        }

        $conclusion = isset($dt_put['conclusion']) ? $this->security->xss_clean((string) $dt_put['conclusion']) : null;
        $order_note = isset($dt_put['notes']) ? $this->security->xss_clean((string) $dt_put['notes']) : null;
        $advice     = isset($dt_put['advice']) ? $this->security->xss_clean((string) $dt_put['advice']) : null;

        $this->db->trans_begin();
        $now     = date('Y-m-d H:i:s');
        $saved   = 0;
        $failed  = array();

        foreach ($plan as $item_id => $row) {
            $upd = array('status' => 3, 'date_result' => $now, 'is_delete' => 0);
            if ($row['result'] !== null) $upd['notes'] = $row['result'];
            if ($row['notes'] !== null)  $upd['notes'] = trim((string) $row['notes'] . ' ' . (string) $upd['notes']);
            if ($row['url'] !== null)    $upd['url'] = $row['url'];

            $this->db->where('id', $item_id);
            $this->db->where('radiology_request_id', $id);
            if ($this->db->update('radiology_request_item', $upd) === FALSE) {
                $failed[] = array('radiology_request_item_id' => $item_id, 'reason' => 'update failed',
                    'db_error' => $this->db->error()['message']);
                continue;
            }
            $saved++;

            // Lampiran/gambar: satu baris per item (replace), hanya bila ada url.
            if ($row['url'] !== null) {
                $this->db->where('radiology_request_item_id', $item_id);
                $exists = $this->db->get('radiology_request_item_image')->row();
                $img = array(
                    'url'         => $row['url'],
                    'conclusion'  => $conclusion,
                    'notes'       => $row['result'],
                    'advice'      => $advice,
                    'status'      => 3,
                    'date_result' => $now,
                    'user_lab_id' => $user_id,
                    'is_delete'   => 0,
                );
                if ($exists) {
                    $this->db->where('id', $exists->id);
                    $this->db->update('radiology_request_item_image', $img);
                } else {
                    $this->db->insert('radiology_request_item_image', array_merge($img, array(
                        'id'                       => $this->rad_uuid4(),
                        'radiology_request_item_id' => $item_id,
                        'date_added'               => $now,
                        'date_request'             => $now,
                        'date_process'             => $now,
                    )));
                }
            }
        }

        $complete = $this->rad_order_fully_resulted($id);

        if (count($failed) > 0) {
            $this->db->trans_rollback();
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', array(
                'id' => $id, 'saved' => 0, 'failed' => $failed,
            ));
            return;
        }

        // Header order: narasi hanya ditimpa bila dikirim (null = biarkan nilai lama).
        $hdr = array();
        if ($complete) {
            $hdr['status']      = 3;
            $hdr['date_result'] = $now;
            $hdr['user_lab_id'] = $user_id;
        }
        if ($conclusion !== null) $hdr['conclusion'] = $conclusion;
        if ($order_note !== null) $hdr['notes']      = $order_note;
        if ($advice !== null)     $hdr['advice']     = $advice;
        if (count($hdr) > 0) {
            $this->db->where('id', $id);
            $this->db->update('radiology_request', $hdr);
        }

        $this->db->trans_commit();

        $this->REST_Return(200, 'SUCCESS', array(
            'id'           => $id,
            'saved'        => $saved,
            'items'        => count($plan),
            'order_closed' => $complete ? 1 : 0,
            'failed'       => array(),
        ));
        return;
    }

    /* ---------------- helpers ---------------- */

    private function load_rad_order_items($order_id)
    {
        $this->db->select('a.id, a.radiology_request_id, a.radiology_package_id, a.service_class_id,
                           a.costing_id, a.costing_name, a.status, a.date_request, a.date_process,
                           a.date_result, a.notes, a.url');
        $this->db->from('radiology_request_item a');
        $this->db->where('a.radiology_request_id', $order_id);
        $this->db->where('a.is_delete', 0);
        $this->db->order_by('a.costing_name', 'ASC');
        return $this->db->get()->result();
    }

    private function build_rad_result_entry($items)
    {
        $out = array();
        foreach ($items as $item) {
            $out[] = array(
                'radiology_request_item_id' => (string) $item->id,
                'radiology_package_id'      => $item->radiology_package_id,
                'costing_name'              => $item->costing_name,
                'status'                    => (int) $item->status,
                'date_request'              => $item->date_request,
                'date_process'              => $item->date_process,
                'date_result'               => $item->date_result,
                'result'                    => $item->notes,
                'url'                       => $item->url,
                'has_result'                => $item->date_result ? 1 : 0,
            );
        }
        return $out;
    }

    /** Order selesai hanya bila setiap item non-deleted punya date_result. */
    private function rad_order_fully_resulted($order_id)
    {
        $this->db->where('radiology_request_id', $order_id);
        $this->db->where('is_delete', 0);
        $this->db->where('date_result IS NULL', null, FALSE);
        if ($this->db->count_all_results('radiology_request_item') > 0) return false;

        $this->db->where('radiology_request_id', $order_id);
        $this->db->where('is_delete', 0);
        if ($this->db->count_all_results('radiology_request_item') === 0) return false;
        return true;
    }

    private function rad_uuid4()
    {
        $d = random_bytes(16);
        $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
        $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
    }
}
