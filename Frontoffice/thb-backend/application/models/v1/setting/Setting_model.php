<?php class Setting_model extends MY_Model {
	protected $tableName = 'setting';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>