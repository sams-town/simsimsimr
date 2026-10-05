<?php class Monitored_anesthesia_model extends MY_Model {
	protected $tableName = 'monitored_anesthesia';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>