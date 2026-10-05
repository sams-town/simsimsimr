<?php class Nutrition_model extends MY_Model {
	protected $tableName = 'nutrition';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, hfi.name as health_facility_item_name, p.name as patient_name, h.name as alias_name, p.birth_date, r.norm, e.fullname as employee_nurse_name, e2.fullname as employee_approved_name, ro.name as room_name, d.name as diet_name";
	protected $joinFields = array(
		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
		array('registration r', 'rd.registration_id = r.id', 'INNER'),
		array('patient p', 'r.norm = p.norm', 'INNER'),
		array('helper h', 'p.alias = h.code AND h.variable="alias"', 'INNER'),
		array('room ro', 'a.room_id = ro.id', 'INNER'),
		array('employee e', 'a.employee_nurse_id = e.id', 'INNER'),
		array('employee e2', 'a.employee_approved_id = e2.id', 'LEFT'),
		array('diet d', 'a.diet_id = d.id', 'INNER'),
		array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
	);
}?>