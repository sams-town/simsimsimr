<?php class Appointment_schedule_model extends MY_Model {
	protected $tableName = 'appointment_schedule';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*,b.date_appointment,b.name as patient_name";
	protected $joinFields = array(
        array('appointment b', 'a.appointment_id = b.id', 'LEFT'),
	);
}?>