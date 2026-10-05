<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CitrPersiapanEdukasi_model extends MY_Model
{
    protected $tableName = 'citr_persiapan_edukasi';
    protected $resultMode = 'object';
    public $primaryKey = 'id';
    public $whereFields;
    public $limit;
    public $offset;
    public function insert($data)
    {
        return $this->db->insert('citr_persiapan_edukasi', $data);
    }

    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('citr_persiapan_edukasi')->row();
    }

    public function get_by_citr_id($citr_id)
    {
        $this->db->where('citr_id', $citr_id);
        return $this->db->get('citr_persiapan_edukasi')->result();
    }

    public function update_by_citr_id($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('citr_persiapan_edukasi', $data);
    }

    public function delete_by_citr_id($citr_id)
    {
        $this->db->where('citr_id', $citr_id);
        return $this->db->delete('citr_persiapan_edukasi');
    }
}
