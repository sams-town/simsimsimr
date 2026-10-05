<?php class Consultation_request_model extends MY_Model {
	protected $tableName = 'consultation_request';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, e.fullname as doctor_in_charge_name";
	protected $joinFields = array(
		array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
	);
}?>