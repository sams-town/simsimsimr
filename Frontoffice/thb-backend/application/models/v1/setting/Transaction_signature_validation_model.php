<?php class Transaction_signature_validation_model extends MY_Model {
	protected $tableName = 'transaction_signature_validation';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,b.name as group_name, c.username as user_name";
	protected $joinFields = array(
		array('group b','a.group_id = b.id', 'LEFT'),
		array('user c','a.user_id = c.id', 'LEFT'),
	);
}?>