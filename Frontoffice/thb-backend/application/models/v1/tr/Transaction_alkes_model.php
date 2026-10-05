<?php class Transaction_alkes_model extends MY_Model {
	protected $tableName = 'transaction_alkes';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,hfi.name as health_facility_item_name";
	protected $joinFields = array(
		array('registration_destination b', 'a.registration_destination_id = b.id', 'LEFT'),
		array('health_facility_item hfi', 'b.health_facility_item_id = hfi.id', 'LEFT'),
		array('registration c', 'b.registration_id = c.id', 'LEFT'),
	);
}?>