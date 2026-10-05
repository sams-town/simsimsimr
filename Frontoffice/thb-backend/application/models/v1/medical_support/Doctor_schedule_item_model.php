<?php class Doctor_schedule_item_model extends MY_Model {
	protected $tableName = 'doctor_schedule_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.day' => 'ASC');
	protected $selectFields = "a.*,b.name as day_name,c.code,c.doctor_id as doctor_id,d.name as doctor_name,d.bride_bpjs,
								c.health_facility_item_id,e.name as health_facility_item_name,
								e.health_facility_id,f.name as health_facility_name,e.bride_bpjs as bride_bpjs_poli,e.bride_bpjsspecialist";
	protected $joinFields = array(
        array('helper b', 'a.day = b.code AND variable="day"', 'LEFT'),
        array('doctor_schedule c', 'a.doctor_schedule_id = c.id', 'LEFT'),
        array('employee d', 'c.doctor_id = d.id', 'LEFT'),
        array('health_facility_item e', 'c.health_facility_item_id = e.id', 'LEFT'),
        array('health_facility f', 'e.health_facility_id = f.id', 'LEFT'),
    );
}?>