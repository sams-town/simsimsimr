<?php class Downpayment_item_model extends MY_Model {
	protected $tableName = 'downpayment_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'downpayment_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.downpayment_id' => 'ASC');
	protected $selectFields = "a.*,b.name as payment_method_name";
	protected $joinFields = array(
		array('payment_method b', 'a.payment_method_id = b.id', 'LEFT'),
    );
}?>