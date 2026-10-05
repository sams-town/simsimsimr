<?php class Display_poli_model extends MY_Model {
	protected $tableName = 'display_poli';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,ds.code,e.fullname as doctor_name,hf.name as health_facility_item_name";
	protected $joinFields = array(
		array('doctor_schedule ds', 'a.doctor_schedule_id = ds.id', 'LEFT'),
		array('employee e', 'ds.doctor_id = e.id', 'LEFT'),
		array('health_facility_item hf', 'ds.health_facility_item_id = hf.id', 'LEFT'),
	);
}?>