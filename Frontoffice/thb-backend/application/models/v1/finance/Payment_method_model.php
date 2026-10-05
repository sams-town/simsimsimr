<?php class Payment_method_model extends MY_Model {
	protected $tableName = 'payment_method';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.name' => 'ASC');
	protected $selectFields = "a.*,b.name as type_name";
	protected $joinFields = array(
		array('helper b', 'a.type_id = b.code AND b.variable="type_payment_method"', 'LEFT'),
	);
}?>