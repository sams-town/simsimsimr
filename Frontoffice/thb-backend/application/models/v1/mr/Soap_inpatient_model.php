<?php class Soap_inpatient_model extends MY_Model {
	protected $tableName = 'soap_inpatient';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,b.fullname as employee_doctor_name,b_from.fullname as employee_doctor_name_from,b_from.ttd as employee_signature,l.name as type_room_name,r.norm,hfi.health_facility_id";
	protected $joinFields = array(
		array('soap s', 'a.soap_id = s.id', 'LEFT'),
		array('registration_destination rd', 's.registration_destination_id = rd.id', 'LEFT'),
		array('registration r', 'rd.registration_id = r.id', 'LEFT'),
		array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'LEFT'),
		array('employee b', 'a.doctor_in_charge_id = b.id', 'LEFT'),
		array('employee b_from', 's.doctor_in_charge_id = b_from.id', 'LEFT'),
		array('helper l', 'a.type_room_id = l.code AND l.variable="type_room"', 'LEFT'),
	);
	}?>