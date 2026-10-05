<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ChildSkalaNyeri_model extends MY_Model
{
    protected $tableName = 'child_skala_nyeri';
    protected $resultMode = 'object';
    public $primaryKey = 'id';

    public function insert($data)
    {
        return $this->db->insert($this->tableName, $data);
    }

    public function get_nyeri_by_register_destination_id($query)
    {
        $this->db->where('register_destination_id', $query['register_destination_id']);
        return $this->db->get($this->tableName)->result_array();
    }

    public function get_by_id($id)
    {
        $this->db->where($this->primaryKey, $id);
        return $this->db->get($this->tableName)->row();
    }

    public function update_by_id($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->tableName, $data);
    }

    public function get_by_child_id($child_id)
    {
        $this->db->where('child_id', $child_id);
        return $this->db->get($this->tableName)->result();
    }

    public function update_by_child_id($id, $data)
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
