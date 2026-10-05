<?php class Monitoring_kala_four_model extends MY_Model {
	protected $tableName = 'monitoring_kala_four';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.time_id' => 'ASC', 'a.time' => 'ASC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>