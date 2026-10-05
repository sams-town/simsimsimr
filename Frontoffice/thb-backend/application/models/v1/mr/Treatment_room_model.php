<?php class Treatment_room_model extends MY_Model {
	protected $tableName = 'treatment_room';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, b.name as bed_name, r.id as room_id, r.name as room_name, e.fullname as doctor_in_charge_name, e2.fullname as doctor_in_charge_name_2, e3.fullname as doctor_in_charge_name_3, e4.fullname as doctor_in_charge_name_4, e5.fullname as doctor_in_charge_name_5, e6.fullname as doctor_in_charge_name_6, e7.fullname as doctor_in_charge_name_7, e8.fullname as doctor_in_charge_name_8";
	protected $joinFields = array(
		array('bed b', 'a.bed_id = b.id', 'INNER'),
		array('room r', 'b.room_id = r.id', 'INNER'),
		array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
		array('employee e2', 'a.doctor_in_charge_id_2 = e2.id', 'LEFT'),
		array('employee e3', 'a.doctor_in_charge_id_3 = e3.id', 'LEFT'),
		array('employee e4', 'a.doctor_in_charge_id_4 = e4.id', 'LEFT'),
		array('employee e5', 'a.doctor_in_charge_id_5 = e5.id', 'LEFT'),
		array('employee e6', 'a.doctor_in_charge_id_6 = e6.id', 'LEFT'),
		array('employee e7', 'a.doctor_in_charge_id_7 = e7.id', 'LEFT'),
		array('employee e8', 'a.doctor_in_charge_id_8 = e8.id', 'LEFT'),
		array('employee e9', 'a.doctor_in_charge_id_9 = e9.id', 'LEFT'),
		array('employee e10', 'a.doctor_in_charge_id_10 = e10.id', 'LEFT'),
		array('employee e11', 'a.doctor_in_charge_id_11 = e11.id', 'LEFT'),
		array('employee e12', 'a.doctor_in_charge_id_12 = e12.id', 'LEFT'),
	);
}?>