<?php class Loket_model extends MY_Model {
	protected $tableName = 'loket';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.name' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>