<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ChildRiwayatImunisasi_model extends MY_Model
{
    protected $tableName = 'child_riwayat_imunisasi';
    protected $resultMode = 'object';
    public $primaryKey = 'id';

    public function insert($data)
    {
        return $this->db->insert_batch($this->tableName, $data);
    }

    public function get_by_id($id)
    {
        $this->db->where($this->primaryKey, $id);
        return $this->db->get($this->tableName)->row();
    }

    public function validate_existing_by_record($data, $child_id)
    {
        $this->db->where('nama_vaksin', $data);
        $this->db->where('child_id', $child_id);
        return $this->db->get($this->tableName)->row();
    }

    public function get_by_child_id($child_id)
    {
        $this->db->where('child_id', $child_id);
        return $this->db->get($this->tableName)->result();
    }

    public function update_by_id($id, $data)
    {
        $this->db->where('child_id', $id);
        return $this->db->update($this->tableName, $data);
    }

    public function delete_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->tableName);
    }
}
