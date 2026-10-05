<?php class Group_module_model extends MY_Model {
	protected $tableName = 'group_module';
	protected $resultMode = 'object';	  
	public $primaryKey = 'group_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.group_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>
