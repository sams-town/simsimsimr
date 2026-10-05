<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ChildRisikoJatuh_model extends MY_Model
{
    protected $tableName = 'child_resiko_jatuh';
    protected $resultMode = 'object';
    public $primaryKey = 'id';

    public function update_by_id($id, $data)
    {
        $this->db->where('child_id', $id);
        return $this->db->update($this->tableName, $data);
    }

    public function get_by_child_id($child_id)
    {
        $this->db->where('child_id', $child_id);
        return $this->db->get($this->tableName)->row();
    }
}
