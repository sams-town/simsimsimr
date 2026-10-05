<?php class Signatura_dose_model extends MY_Model {
	protected $tableName = 'signatura_dose';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*";
	protected $joinFields = array();
}