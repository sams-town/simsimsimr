<?php class Nurse_soap_model extends MY_Model {
	protected $tableName = 'nurse_soap';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,e.fullname as user_added";
	protected $joinFields = array(
		array('user u', 'a.user_added = u.id', 'LEFT'),
		array('employee e', 'u.employee_id = e.id', 'LEFT'),
	);
}?>