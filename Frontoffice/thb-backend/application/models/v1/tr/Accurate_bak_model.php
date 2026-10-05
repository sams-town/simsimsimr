<?php class Accurate_bak_model extends MY_Model {
	protected $tableName = 'accurate_bak';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "";
	protected $joinFields = array();
}?>