<?php class Transaction_nurse_model extends MY_Model {
	protected $tableName = 'transaction_nurse';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,c.id as registration_destination_id,d.id as registration_id, e.fullname as employee_nurse_name,hfi.name as health_facility_item_name";
	protected $joinFields = array(
		array('nurse b', 'a.nurse_id = b.id', 'LEFT'),
		array('registration_destination c', 'b.registration_destination_id = c.id', 'LEFT'),
		array('employee e', 'a.user_nurse_id = e.id', 'LEFT'),
		array('employee_position ep', 'e.employee_id = ep.employee_id', 'LEFT'),
		array('position p', 'ep.position_id = p.id', 'LEFT'),
		array('health_facility_item hfi', 'c.health_facility_item_id = hfi.id', 'LEFT'),
		array('registration d', 'c.registration_id = d.id', 'LEFT'),
	);
}?>