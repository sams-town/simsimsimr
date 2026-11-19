<?php
/**
 * FASE 2 — order-level laboratory result entry (LIVE).
 *
 * Trait used by controllers/v1/health_facility/Laboratory_request.php.
 *
 * ENDPOINTS
 *   GET  version/health_facility/laboratory_request/result_entry/(:any)   -> order id
 *        One payload with EVERY item of the order + its parameters, so the UI
 *        can fill the whole order on one screen.
 *   PUT  version/health_facility/laboratory_request/result_entry_batch/(:any) -> order id
 *        body: { "items": [ { "laboratory_request_item_id": "<item id>",
 *                             "results": [ { "laboratory_parameter_id": "<param id>",
 *                                            "result": "<value>", "notes": "..." } ] } ],
 *                "conclusion": "", "notes": "", "advice": "" }
 *
 * RULES (mirrors sampling_batch_put)
 *   - ALL items must belong to the order id in the path. Any foreign item id
 *     aborts the whole request (422) — no partial write.
 *   - One transaction; any failing row rolls the batch back (422).
 *   - laboratory_request_item.id is NEVER confused with laboratory_request.id.
 *   - Parameters keep their originating item (laboratory_request_item_id column).
 *   - Request is marked status=3 + date_result only when every non-deleted item
 *     of the order ends up with at least one result row.
 */
trait Laboratory_request_result_entry_trait
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

        $request = $this->Laboratory_request->read($id);
        if (!$request) {
            $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' does not exist!');
            return;
        }

        $items = $this->load_order_items($id);
        if (!$items) {
            $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' has no item!');
            return;
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'id'         => $id,
            'no_request' => isset($request[0]->no_request) ? $request[0]->no_request : null,
            'status'     => isset($request[0]->status) ? (int) $request[0]->status : null,
            'date_request' => isset($request[0]->date_request) ? $request[0]->date_request : null,
            'items'      => $this->build_result_entry($items),
        ));
        return;
    }

    /** PUT result_entry_batch/(:any) */
    /**
     * PUT result_entry_order/(:any)
     * Canonical name required by the order-level contract. Thin alias so the
     * route exists by that name too; the body is the SAME tested implementation
     * (result_entry_batch_put) — no duplicated logic.
     */
    public function result_entry_order_put($p = 0)
    {
        return $this->result_entry_batch_put($p);
    }

    public function result_entry_batch_put($p = 0)
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
            // An unknown/garbled order id is a 404, not a 422 format error.
            $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' does not exist!');
            return;
        }
        $id = $this->security->xss_clean($id);

        $request = $this->Laboratory_request->read($id);
        if (!$request) {
            $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' does not exist!');
            return;
        }
        if (!$this->load_order_items($id)) {
            $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' has no item!');
            return;
        }

        $payload_items = isset($dt_put['items']) ? $dt_put['items'] : null;
        if (!is_array($payload_items) OR count($payload_items) === 0) {
            $this->REST_Return(422, 'Format error', array('items' => 'items must be a non-empty array.'));
            return;
        }

        $items = $this->load_order_items($id);
        if (!$items) {
            $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' has no item!');
            return;
        }

        // --- Pre-flight: ownership + shape. NOTHING is written before this passes. ---
        $owned_item_ids = array();
        foreach ($items as $item) {
            $owned_item_ids[(string) $item->id] = true;
        }

        $plan   = array();
        $errors = array();
        foreach ($payload_items as $idx => $entry) {
            $item_id = isset($entry['laboratory_request_item_id']) ? trim((string) $entry['laboratory_request_item_id']) : '';
            if ($item_id === '') {
                $errors[] = array('index' => $idx, 'field' => 'laboratory_request_item_id', 'reason' => 'required');
                continue;
            }
            if (!isset($owned_item_ids[$item_id])) {
                // Foreign item or item of another order: whole batch rejected.
                $errors[] = array('index' => $idx, 'field' => 'laboratory_request_item_id', 'reason' => 'item ' . $item_id . ' does not belong to laboratory_request ' . $id);
                continue;
            }
            $rows = isset($entry['results']) ? $entry['results'] : null;
            if (!is_array($rows) OR count($rows) === 0) {
                $errors[] = array('index' => $idx, 'field' => 'results', 'reason' => 'results must be a non-empty array');
                continue;
            }
            $clean = array();
            $row_errors_before = count($errors);
            foreach ($rows as $r_idx => $row) {
                $param_id = isset($row['laboratory_parameter_id']) ? trim((string) $row['laboratory_parameter_id']) : '';
                $result   = isset($row['result']) ? trim((string) $row['result']) : '';
                if ($param_id === '') {
                    $errors[] = array('index' => $idx, 'row' => $r_idx, 'field' => 'laboratory_parameter_id', 'reason' => 'required');
                    continue;
                }
                if ($result === '') {
                    $errors[] = array('index' => $idx, 'row' => $r_idx, 'field' => 'result', 'reason' => 'required', 'laboratory_parameter_id' => $param_id, 'laboratory_request_item_id' => $item_id);
                    continue;
                }
                $clean[] = array(
                    'laboratory_parameter_id' => $param_id,
                    'result'   => $this->security->xss_clean($result),
                    'notes'    => isset($row['notes']) ? $this->security->xss_clean((string) $row['notes']) : null,
                    'value'    => isset($row['value']) ? $this->security->xss_clean((string) $row['value']) : null,
                    'unit'     => isset($row['unit']) ? $this->security->xss_clean((string) $row['unit']) : null,
                    'code'     => isset($row['code']) ? (int) $row['code'] : null,
                    'name'     => isset($row['name']) ? $this->security->xss_clean((string) $row['name']) : null,
                    'sex'      => isset($row['sex']) ? $this->security->xss_clean((string) $row['sex']) : null,
                );
            }
            if (count($clean) === 0) {
                // Keep the specific row-level reason (which parameter was empty);
                // only fall back to a generic message when nothing was reported.
                if (count($errors) === $row_errors_before) {
                    $errors[] = array('index' => $idx, 'field' => 'results', 'reason' => 'no valid result row');
                }
                continue;
            }
            $plan[$item_id] = array('item' => $entry, 'rows' => $clean);
        }

        if (count($errors) > 0) {
            $this->REST_Return(422, 'Required fields are missing or invalid. Nothing was saved.', array(
                'id'     => $id,
                'errors' => $errors,
                'saved'  => 0,
            ));
            return;
        }

        /* Start Transaction — one batch, all or nothing */
        $this->db->trans_begin();

        $now     = date('Y-m-d H:i:s');
        $saved   = 0;
        $failed  = array();

        foreach ($plan as $item_id => $job) {
            $item = $job['item'];
            foreach ($job['rows'] as $row) {
                $param_id = $row['laboratory_parameter_id'];

                // Keep the item's own package/collation context on the row.
                $this->db->select('a.id, a.laboratory_package_id, a.status');
                $this->db->from('laboratory_request_item a');
                $this->db->where('a.id', $item_id);
                $this->db->where('a.laboratory_request_id', $id);
                $item_row = $this->db->get()->row();

                $this->db->select('p.id, p.code, p.name, p.value, p.unit, p.sex, p.laboratory_collation_id');
                $this->db->from('laboratory_parameter p');
                $this->db->where('p.id', $param_id);
                $param = $this->db->get()->row();
                if (!$param) {
                    $failed[] = array('laboratory_request_item_id' => $item_id, 'laboratory_parameter_id' => $param_id, 'reason' => 'parameter does not exist');
                    continue;
                }

                $upd = array(
                    'result'      => $row['result'],
                    'notes'       => $row['notes'],
                    'status'      => 3,
                    'date_result' => $now,
                    'is_delete'   => 0,
                );
                if ($row['value'] !== null)       $upd['value'] = $row['value'];
                if ($row['unit'] !== null)        $upd['unit'] = $row['unit'];
                if ($row['code'] !== null)        $upd['code'] = $row['code'];
                if ($row['name'] !== null)        $upd['name'] = $row['name'];
                if ($row['sex'] !== null)         $upd['sex'] = $row['sex'];

                $this->db->where('laboratory_request_item_id', $item_id);
                $this->db->where('laboratory_parameter_id', $param_id);
                $exists = $this->db->get('laboratory_request_item_result')->row();

                if ($exists) {
                    $this->db->where('id', $exists->id);
                    $ok = $this->db->update('laboratory_request_item_result', $upd);
                } else {
                    $ins = array_merge($upd, array(
                        'id'                        => $this->uuid4(),
                        'laboratory_request_item_id' => $item_id,
                        'laboratory_package_id'     => $item_row ? $item_row->laboratory_package_id : null,
                        'laboratory_collation_id'   => $param->laboratory_collation_id,
                        'laboratory_parameter_id'   => $param_id,
                        'code'                      => $param->code,
                        'name'                      => $param->name,
                        'value'                     => $param->value,
                        'unit'                      => $param->unit,
                        'sex'                       => $param->sex,
                        'date_request'              => $now,
                        'date_process'              => $now,
                    ));
                    $ok = $this->db->insert('laboratory_request_item_result', $ins);
                }
                if ($ok === FALSE) {
                    $failed[] = array(
                        'laboratory_request_item_id' => $item_id,
                        'laboratory_parameter_id'    => $param_id,
                        'reason'                     => 'write failed',
                        'db_error'                   => $this->db->error()['message'],
                    );
                } else {
                    $saved++;
                }
            }

            // Item reaching status 3 keeps its own timestamp, not the order's.
            $this->db->where('id', $item_id);
            $this->db->where('laboratory_request_id', $id);
            $this->db->update('laboratory_request_item', array(
                'status'      => 3,
                'date_result' => $now,
            ));
        }

        // Order is complete only when every item of the order has result rows.
        $complete = $this->order_fully_resulted($id);

        if (count($failed) > 0) {
            $this->db->trans_rollback();
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', array(
                'id'      => $id,
                'saved'   => 0,
                'failed'  => $failed,
            ));
            return;
        }

        if ($complete) {
            $this->db->where('id', $id);
            $this->db->update('laboratory_request', array(
                'status'      => 3,
                'date_result' => $now,
                'user_lab_id' => $user_id,
            ));
        }

        // Kesimpulan/catatan/saran order. Kontrak di docblock sudah menjanjikan
        // ketiganya tetapi sebelumnya tidak pernah ditulis. Hanya ditimpa bila
        // dikirim, supaya PUT ulang tanpa field ini tidak menghapus nilai lama.
        $hdr = array();
        if (isset($dt_put['conclusion']) && trim((string) $dt_put['conclusion']) !== '')
            $hdr['conclusion'] = $this->security->xss_clean((string) $dt_put['conclusion']);
        if (isset($dt_put['notes']) && trim((string) $dt_put['notes']) !== '')
            $hdr['notes'] = $this->security->xss_clean((string) $dt_put['notes']);
        if (isset($dt_put['advice']) && trim((string) $dt_put['advice']) !== '')
            $hdr['advice'] = $this->security->xss_clean((string) $dt_put['advice']);
        if (count($hdr) > 0) {
            $this->db->where('id', $id);
            $this->db->update('laboratory_request', $hdr);
        }

        $this->db->trans_commit();
        /* End Transaction */

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

    /**
     * GET result_input/(:any)
     * Per-ITEM parameter feed used by ProcessResultView (getResultInputById).
     * Reuses build_result_entry so both flows can never drift apart.
     * Contract: { id: item_id, order_id, parameters: [...] } — parameters is
     * ALWAYS an array, never null, and always carries laboratory_parameter_id
     * + laboratory_request_item_id so the UI never has to guess identity.
     */
    public function result_input_get($p = 0)
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

        // Resolve the ITEM first. laboratory_request.id and
        // laboratory_request_item.id are different entities and must never be
        // silently treated as interchangeable.
        $this->db->select('a.id, a.laboratory_request_id, a.laboratory_package_id, a.service_class_id, a.costing_id, a.costing_name, a.status, a.date_request, a.date_process, a.date_result, a.date_sampling, a.notes');
        $this->db->from('laboratory_request_item a');
        $this->db->where('a.id', $id);
        $this->db->where('a.is_delete', 0);
        $item = $this->db->get()->result();

        if (!$item) {
            $this->REST_Return(404, 'Laboratory_request_item with id ' . $id . ' does not exist!');
            return;
        }

        $entry = $this->build_result_entry($item);
        $params = isset($entry[0]['parameters']) ? $entry[0]['parameters'] : array();

        // Group by collation for the legacy per-item view (ProcessResultView reads
        // `.collation[].parameter[]`). Grouping is DERIVED from the parameters the
        // DB returned — never from a hardcoded list. Order preserved as returned.
        $collation = array();
        foreach ($params as $p) {
            $cid = (string) ($p['laboratory_collation_id'] === null ? '' : $p['laboratory_collation_id']);
            if (!isset($collation[$cid])) {
                $collation[$cid] = array(
                    'collation_id'   => $cid,
                    'collation_name' => isset($p['collation_name']) ? $p['collation_name'] : null,
                    'parameter'      => array(),
                );
            }
            $collation[$cid]['parameter'][] = $p;
        }

        $this->REST_Return(200, 'SUCCESS', array(
            'id'        => (string) $item[0]->id,
            'order_id'  => (string) $item[0]->laboratory_request_id,
            'item'      => $entry[0],
            'parameters' => $params,
            'collation'  => array_values($collation),
            'diagnostic' => $params
                ? null
                : 'Pemeriksaan ini belum memiliki konfigurasi parameter pada master laboratorium.',
        ));
        return;
    }

    private function load_order_items($order_id)
    {
        $this->db->select('a.id, a.laboratory_request_id, a.laboratory_package_id, a.service_class_id, a.costing_id, a.costing_name, a.status, a.date_request, a.date_process, a.date_result, a.date_sampling, a.notes');
        $this->db->from('laboratory_request_item a');
        $this->db->where('a.laboratory_request_id', $order_id);
        $this->db->where('a.is_delete', 0);
        $this->db->order_by('a.costing_name', 'ASC');
        return $this->db->get()->result();
    }

    private function build_result_entry($items)
    {
        $out = array();
        foreach ($items as $item) {
            $item_id = (string) $item->id;

            // Every parameter registered for this item's package.
            // SOURCE OF TRUTH = laboratory_package_item (package -> collation) joined to
            // laboratory_parameter. Sebelumnya memakai laboratory_collation_parameter,
            // yang di schema ini KOSONG (0 baris) sehingga setiap order menghasilkan
            // parameters=[] dan layar hasil selalu kosong. Fallback ke
            // laboratory_collation_parameter hanya bila package_item tidak punya baris.
            $this->db->select('p.id as laboratory_parameter_id, p.code, p.name, p.value, p.unit, p.sex, p.laboratory_collation_id, c.name as collation_name');
            $this->db->from('laboratory_package_item pi');
            $this->db->join('laboratory_parameter p', 'p.laboratory_collation_id = pi.laboratory_collation_id AND p.is_delete = 0', 'INNER');
            $this->db->join('laboratory_collation c', 'c.id = pi.laboratory_collation_id', 'LEFT');
            $this->db->where('pi.laboratory_package_id', $item->laboratory_package_id);
            $this->db->where('pi.is_delete', 0);
            $this->db->order_by('c.name', 'ASC');
            $this->db->order_by('p.code', 'ASC');
            $params = $this->db->get()->result();

            if (!$params) {
                $this->db->select('p.id as laboratory_parameter_id, p.code, p.name, p.value, p.unit, p.sex, p.laboratory_collation_id, c.name as collation_name');
                $this->db->from('laboratory_collation_parameter cp');
                $this->db->join('laboratory_parameter p', 'p.laboratory_collation_id = cp.laboratory_collation_id AND p.is_delete = 0', 'INNER');
                $this->db->join('laboratory_collation c', 'c.id = cp.laboratory_collation_id', 'LEFT');
                $this->db->where('cp.laboratory_package_id', $item->laboratory_package_id);
                $this->db->where('cp.is_delete', 0);
                $this->db->order_by('c.name', 'ASC');
                $this->db->order_by('p.code', 'ASC');
                $params = $this->db->get()->result();
            }

            // Existing results keyed by parameter id (one row per param).
            $this->db->select('r.id, r.laboratory_parameter_id, r.result, r.value, r.unit, r.notes, r.status, r.date_result');
            $this->db->from('laboratory_request_item_result r');
            $this->db->where('r.laboratory_request_item_id', $item_id);
            $this->db->where('r.is_delete', 0);
            $existing = array();
            foreach ($this->db->get()->result() as $row) {
                $existing[(string) $row->laboratory_parameter_id] = $row;
            }

            $params_out = array();
            foreach ($params as $param) {
                $pid = (string) $param->laboratory_parameter_id;
                $prev = isset($existing[$pid]) ? $existing[$pid] : null;
                $params_out[] = array(
                    'laboratory_parameter_id' => $pid,
                    'laboratory_collation_id' => isset($param->laboratory_collation_id) ? (string) $param->laboratory_collation_id : null,
                    'collation_name' => isset($param->collation_name) ? $param->collation_name : null,
                    'code'        => $param->code,
                    'name'        => $param->name,
                    'value'       => $prev ? $prev->value : $param->value,
                    'unit'        => $prev ? $prev->unit : $param->unit,
                    'sex'         => $param->sex,
                    'result'      => $prev ? $prev->result : null,
                    'notes'       => $prev ? $prev->notes : null,
                    'has_result'  => $prev ? 1 : 0,
                    'date_result' => $prev ? $prev->date_result : null,
                );
            }

            $out[] = array(
                'laboratory_request_item_id' => $item_id,
                'laboratory_package_id'      => $item->laboratory_package_id,
                'costing_name'               => $item->costing_name,
                'status'                     => (int) $item->status,
                'date_sampling'              => $item->date_sampling,
                'date_process'               => $item->date_process,
                'date_result'                => $item->date_result,
                'parameters'                 => $params_out,
            );
        }
        return $out;
    }

    private function order_fully_resulted($order_id)
    {
        $this->db->select('a.id');
        $this->db->from('laboratory_request_item a');
        $this->db->where('a.laboratory_request_id', $order_id);
        $this->db->where('a.is_delete', 0);
        $items = $this->db->get()->result();
        if (!$items) return false;

        foreach ($items as $item) {
            $this->db->select('r.id');
            $this->db->from('laboratory_request_item_result r');
            $this->db->where('r.laboratory_request_item_id', $item->id);
            $this->db->where('r.is_delete', 0);
            $this->db->where('r.result IS NOT NULL', null, false);
            if ($this->db->count_all_results() === 0) return false;
        }
        return true;
    }

    private function uuid4()
    {
        $d = random_bytes(16);
        $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
        $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
    }
}
