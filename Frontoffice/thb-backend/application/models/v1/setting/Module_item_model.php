<?php class Module_item_model extends MY_Model {
	protected $tableName = 'module_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,b.name as module_name";
	protected $joinFields = array(
		array('module b', 'a.module_id = b.id', 'LEFT'),
	);
}?>