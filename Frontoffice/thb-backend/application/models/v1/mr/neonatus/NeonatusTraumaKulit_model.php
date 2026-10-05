<?php
defined('BASEPATH') or exit('No direct script access allowed');

class NeonatusTraumaKulit_model extends MY_Model
{
    protected $tableName = 'neonatus_pemeriksaan_fisik_trauma_kulit';
    protected $resultMode = 'object';
    public $primaryKey = 'id';
    public $whereFields;
    public $limit;
    public $offset;

    public function get_by_neonatus_id($neonatus_id)
    {
        $this->db->where('neonatus_id', $neonatus_id);
        return $this->db->get($this->tableName)->row();
    }

    public function update_by_id($id, $data)
    {
        $this->db->where('neonatus_id', $id);
        return $this->db->update($this->tableName, $data);
    }
}
