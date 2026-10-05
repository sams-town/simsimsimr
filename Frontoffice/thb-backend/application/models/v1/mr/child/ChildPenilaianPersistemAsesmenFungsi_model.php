<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ChildPenilaianPersistemAsesmenFungsi_model extends MY_Model
{
    protected $tableName = 'child_penilaian_persistem_asesmen_fungsi';
    protected $resultMode = 'object';
    public $primaryKey = 'id';

    public function insert($data)
    {
        return $this->db->insert($this->tableName, $data);
    }

    public function get_by_id($id)
    {
        $this->db->where($this->primaryKey, $id);
        return $this->db->get($this->tableName)->row();
    }

    public function get_by_child_id($child_id)
    {
        $this->db->where('child_id', $child_id);
        return $this->db->get($this->tableName)->row();
    }

    public function update_by_id($id, $data)
    {
        $this->db->where('child_id', $id);
        return $this->db->update($this->tableName, $data);
    }

    public function delete_by_child_id($child_id)
    {
        $this->db->where('child_id', $child_id);
        return $this->db->delete($this->tableName);
    }
}
