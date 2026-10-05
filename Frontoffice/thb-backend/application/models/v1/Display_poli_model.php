<?php class Display_poli_model extends MY_Model {
	protected $tableName = 'display_poli';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,ds.doctor_id,ds.code,e.fullname as doctor_name,hf.name as health_facility_item_name,e.photo_sm,e.photo_md,dsi.hour_start,dsi.hour_end,dsi.day";
	protected $joinFields = array(
		array('display d', 'a.display_id = d.id', 'LEFT'),
		array('doctor_schedule ds', 'a.doctor_schedule_id = ds.id', 'LEFT'),
		array('doctor_schedule_item dsi', 'a.doctor_schedule_id = dsi.doctor_schedule_id', 'LEFT'),
		array('employee e', 'ds.doctor_id = e.id', 'LEFT'),
		array('health_facility_item hf', 'ds.health_facility_item_id = hf.id', 'LEFT'),
	);
}?>