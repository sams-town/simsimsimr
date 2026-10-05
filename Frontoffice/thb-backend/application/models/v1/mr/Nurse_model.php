<?php class Nurse_model extends MY_Model {
	protected $tableName = 'nurse';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,b.name as employee_nurse_name,c.name as type_nurse_name, hf.id as health_facility_id, hf.name as health_facility_name, hfi.id as health_facility_item_id, hfi.name as health_facility_item_name";
	protected $joinFields = array(
		array('employee b', 'a.employee_nurse_id = b.id', 'LEFT'),
		array('helper c', 'a.type_nurse_id = c.code AND c.variable = "nurse"', 'LEFT'),
		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
		array('registration r', 'rd.registration_id = r.id', 'LEFT'),
		array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'LEFT'),
		array('health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'),
	);
}?>