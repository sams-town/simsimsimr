<?php class Group_module_item_model extends MY_Model {
	protected $tableName = 'group_module_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'group_id';	
	public $whereFields;
	protected $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>
