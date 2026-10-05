<?php class Signature_validation_item_model extends MY_Model {
	protected $tableName = 'signature_validation_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,b.name as group_name";
	protected $joinFields = array(
		array('group b','a.group_id = b.id', 'LEFT'),
	);
}?>