<?php class Transaction_blood_model extends MY_Model {
	protected $tableName = 'transaction_blood';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,";
	protected $joinFields = array(
		array('registration_destination b', 'a.registration_destination_id = b.id', 'LEFT'),
		array('registration c', 'b.registration_id = c.id', 'LEFT'),
	);
}?>