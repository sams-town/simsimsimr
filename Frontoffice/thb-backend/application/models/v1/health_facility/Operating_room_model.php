<?php class Operating_room_model extends MY_Model {
	protected $tableName = 'operating_room';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,p.norm, p.fullname as patient_name, p.birth_date, j.name as sex_name, h.name as alias_name, b.name as bed_name, rm.id as room_id, rm.name as room_name, e.fullname as employee_approved_name";
	protected $joinFields = array(
		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
		array('registration r', 'rd.registration_id = r.id', 'INNER'),
		array('patient p', 'r.norm = p.norm', 'INNER'),
		array('helper h', "p.alias = h.code and variable='alias'", 'INNER'),
		array('helper j', 'p.sex = j.code AND j.variable="sex"', 'INNER'),
		array('bed b', 'a.bed_id = b.id', 'LEFT'),
		array('room rm', 'b.room_id = rm.id', 'LEFT'),
		array('employee e', 'a.employee_approved_id = e.id', 'LEFT'),
	);
}?>