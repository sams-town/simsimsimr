<?php class Registration_destination_model extends MY_Model {
	protected $tableName = 'registration_destination';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,b.name as bed_name,c.name as room_name,d.name as doctor_in_charge_name,e.name as doctor_in_charge_sec_name,hfi.health_facility_id,hfi.bride_bpjs,r.code_booking,r.norm";
	protected $joinFields = array(
		array('registration r', 'a.registration_id = r.id', 'LEFT'),
		array('bed b', 'a.bed_id = b.id', 'LEFT'),
		array('room c', 'b.room_id = c.id', 'LEFT'),
		array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
		array('employee d', 'a.doctor_in_charge_id = d.id', 'LEFT'),
		array('employee e', 'a.doctor_in_charge_sec_id = e.id', 'LEFT'),
	);
}?>