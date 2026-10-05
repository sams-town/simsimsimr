<?php class Soap_model extends MY_Model {
	protected $tableName = 'soap';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	public $selectFields = "a.*,r.id as registration_id, r.register_date, b.fullname as employee_doctor_name, c.fullname as doctor_in_charge_name_1, d.fullname as doctor_in_charge_name_2, e.fullname as doctor_in_charge_name_3, f.fullname as doctor_in_charge_name_4, g.fullname as doctor_in_charge_name_5,b.ttd as employee_signature,r.norm,hfi.id as health_facility_item_id,hfi.name as health_facility_item_name, hf.id as health_facility_id,hf.name as health_facility_name, rd.checkin_date, rd.register_date";
	protected $joinFields = array(
		array('employee b', 'a.doctor_in_charge_id = b.id', 'LEFT'),
		array('employee c', 'a.doctor_in_charge_id_1 = c.id', 'LEFT'),
		array('employee d', 'a.doctor_in_charge_id_2 = d.id', 'LEFT'),
		array('employee e', 'a.doctor_in_charge_id_3 = e.id', 'LEFT'),
		array('employee f', 'a.doctor_in_charge_id_4 = f.id', 'LEFT'),
		array('employee g', 'a.doctor_in_charge_id_5 = g.id', 'LEFT'),
		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
		array('registration r', 'rd.registration_id = r.id', 'LEFT'),
		array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'LEFT'),
		array('health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'),
	);
}?>