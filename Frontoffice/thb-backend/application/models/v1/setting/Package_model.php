<?php class Package_model extends MY_Model {
	protected $tableName = 'package';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.name' => 'ASC');
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>