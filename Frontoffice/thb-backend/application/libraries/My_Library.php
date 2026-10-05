<?php
defined('BASEPATH') or exit('No direct script access allowed');

class My_Library
{

    /**
     * update or create can be for single table and multiple table(Relationship Table)
     */
    public function update_or_create($request, $data, $model, $foerignKey = null)
    {
        return $this->handle_request_by_id($request) ? $this->do_update_or_create(array_merge(array("$foerignKey" => $request['id']), $data), $model, 'put', $foerignKey) : $this->do_update_or_create($data, $model, 'post', $foerignKey);
    }

    /**
     * validate request if inserting ID or not
     */
    private function handle_request_by_id($request): bool
    {
        if (!empty($request['id'])) return true;
        //default create if not inserting by ID
        return false;
    }

    /**
     * case of by type create or update
     */
    private function do_update_or_create($data, $model, $type, $foerignKey)
    {
        switch ($type) {
            case 'post':
                $model->create($data);
                break;
            case 'put':
                $model->update_by_id($data["$foerignKey"], $data);
                break;
        }
    }
}
