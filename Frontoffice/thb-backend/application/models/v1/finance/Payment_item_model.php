<?php class Payment_item_model extends MY_Model {
	protected $tableName = 'payment_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'payment_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.payment_id' => 'ASC');
	protected $selectFields = "a.*,pm.name as payment_method,h.name as payment_type";
	protected $joinFields = array(
		array('payment_method pm', 'a.payment_method_id = pm.id', 'LEFT'),
		array('payment p', 'a.payment_id = p.id', 'LEFT'),
		array('helper h', 'a.payment_method_type_id = h.code AND h.variable="type_payment"', 'LEFT'),
	);
}?>