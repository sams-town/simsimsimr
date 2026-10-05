<?php

// GANTI 1 Golongan_model -> nama model 
class Counter_queue_model extends MY_Model {
	
	protected $tableName = 'counter_queue';
	protected $resultMode = 'object';	  
	public $primaryKey = 'var_1';	
	protected $selectFields = "a.*";
	protected $joinFields = array();
	protected $orderFields = array('counter_queue'=>'ASC');
	protected $limit = NULL;
	  	  
	public function read ($p = 0, $f = array()) {	
		$this->db->select($this->selectFields);
		$this->db->from("{$this->tableName} as a");
		
		// set join fields
		if(!empty($this->joinFields) && count($this->joinFields) > 0) {
			foreach($this->joinFields as $j) {
				if (!isset($j[2]))
					$this->db->join($j[0], $j[1]);
				else $this->db->join($j[0], $j[1], $j[2]);
			}
		}
		
		//set filter data
		if (count($f) > 0) {
			foreach ($f as $key => $val) 
				$this->db->where($key, $val);
		}				
		//set filter data
		if (!empty($this->whereFields) && count($this->whereFields) > 0) {
			foreach ($this->whereFields as $key => $val) 
				$this->db->where($key, $val);
		}				
		if (!empty($this->primaryKey) && $p > 0)
			$this->db->where('a.'.$this->primaryKey, $p);
					
		// set order fields
		if(!empty($this->orderFields) && count($this->orderFields) > 0) {
			foreach ($this->orderFields as $key => $val) 
				$this->db->order_by($key, $val);
		} else if (!empty($this->primaryKey))
			$this->db->order_by($this->primaryKey, "DESC"); 
			
		//set limit and offset
		$this->db->limit($this->limit); 
		$this->db->offset($this->offset); 
		
		return ($this->db->get()->result());
	}
    
	public function set_to_counter() {
		$this->tableName = 'counter_queue';
		$this->primaryKey = 'var_1';
		$this->limit = 1;
		$this->orderFields = array();
	}
	
	public function do_counter($f, $v) {
		if (count($f) > 0 && count($f) == count($v)) {
			$query_update = "
				INSERT INTO counter_queue (". implode(", ", $f) .")
				VALUES ('". implode("', '", $v) ."')
				ON DUPLICATE KEY UPDATE 
					counter_queue = counter_queue + 1;
			";			
			$where = array();
			foreach($f as $i => $field)
				$where[$i] = " {$field} = '{$v[$i]}' ";
			
			$this->db->trans_start();
			if ( $this->db->query($query_update) ) {
				$query_select = "
				SELECT counter_queue 
				FROM counter_queue 
				WHERE ". implode("AND", $where) ."
				";
				$result = $this->db->query($query_select)->row_array();
				$this->db->trans_complete();
				return $result;
			}
		}
		$this->db->trans_rollback();
		return array();
	}
	
}
?>