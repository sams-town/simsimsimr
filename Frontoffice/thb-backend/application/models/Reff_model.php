<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reff_model extends CI_Model {
    protected $antrean_table = 'reff_id';
    // protected $bride_table = 'bpjs_jkn_booking_sh_md';

    /**
     * Add a new Article
     * @param: {array} Article Data
     */

    public function getById($reff_id = 0) {
        $this->db->select('*');
        $this->db->where('Reff_ID',$reff_id);
        $data = $this->db->get('reff_id')->result();    
        return $data[0];
    }

    public function reffnum(array $data) {
        return $this->db->insert_or_update('reff_id', $data);
    }
}