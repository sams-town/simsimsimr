<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CitrEdukasiLanjutan_model extends MY_Model
{
    protected $tableName = 'citr_edukasi_lanjutan';
    protected $resultMode = 'object';
    public $primaryKey = 'id';
    public $whereFields;
    public $limit;
    public $offset;
    public function insert($data)
    {
        return $this->db->insert('citr_edukasi_lanjutan', $data);
    }

    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('citr_edukasi_lanjutan')->row();
    }

    public function get_by_citr_id($citr_id)
    {
        $this->db->where('citr_id', $citr_id);
        return $this->db->get('citr_edukasi_lanjutan')->result();
    }

    public function update_by_citr_id($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('citr_edukasi_lanjutan', $data);
    }

    public function delete_by_citr_id($citr_id)
    {
        $this->db->where('citr_id', $citr_id);
        return $this->db->delete('citr_edukasi_lanjutan');
    }
}
