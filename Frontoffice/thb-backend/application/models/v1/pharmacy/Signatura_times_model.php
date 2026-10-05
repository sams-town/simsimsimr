<?php class Signatura_times_model extends MY_Model {
	protected $tableName = 'signatura_times';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*";
	protected $joinFields = array();
}