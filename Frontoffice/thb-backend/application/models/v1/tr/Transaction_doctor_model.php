<?php class Transaction_doctor_model extends MY_Model {
	protected $tableName = 'transaction_doctor';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,c.id as registration_destination_id, hfi.name as health_facility_item_name, e.fullname as doctor_in_charge_name";
	protected $joinFields = array(
		array('soap b', 'a.soap_id = b.id', 'LEFT'),
		array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
		array('registration_destination c', 'b.registration_destination_id = c.id', 'LEFT'),
		array('health_facility_item hfi', 'c.health_facility_item_id = hfi.id', 'LEFT'),
		array('registration d', 'c.registration_id = d.id', 'LEFT'),
	);
}?>