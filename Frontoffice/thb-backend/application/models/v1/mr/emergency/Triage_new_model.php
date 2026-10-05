<?php class Triage_new_model extends MY_Model {
	protected $tableName = 'triage_new';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_updated' => 'DESC');
	protected $selectFields = "a.*, e.fullname as employee_nurse_name";
	protected $joinFields = array(
		array('employee e', 'a.employee_nurse_id = e.id', 'LEFT'),
	);
}?>