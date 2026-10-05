<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Citr_model extends MY_Model
{
    protected $tableName = 'citr';
    protected $resultMode = 'object';
    public $primaryKey = 'id';
    public $whereFields;
    public $limit;
    public $offset;
    public function insert($data)
    {
        $this->db->insert('citr', $data); // Perform the insert
        return $this->db->insert_id(); // Fetch and return the inserted ID
    }

    public function get_by_registration_id($id)
    {
        $this->db->where('register_destination_id', $id);
        return $this->db->get('citr')->row();
    }

    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('citr')->row();
    }

    public function update_by_id($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('citr', $data);
    }

    public function delete_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('citr');
    }
}
