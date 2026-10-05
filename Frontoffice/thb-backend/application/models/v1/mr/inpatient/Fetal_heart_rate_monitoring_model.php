<?php class Fetal_heart_rate_monitoring_model extends MY_Model {
	protected $tableName = 'fetal_heart_rate_monitoring';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*, e.fullname as employee_nurse_name";
	protected $joinFields = array(
		array('employee e', 'a.employee_nurse_id = e.id', 'INNER'),
	);
}?>