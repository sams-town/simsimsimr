<?php class Doctor_schedule_model extends MY_Model {
	protected $tableName = 'doctor_schedule';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,b.name as health_facility_item_name,c.fullname as doctor_name";
	protected $joinFields = array(
		array('health_facility_item b', 'a.health_facility_item_id = b.id', 'LEFT'),
		array('employee c', 'a.doctor_id = c.id', 'LEFT'),
	);
}?>