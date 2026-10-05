<?php class Employee_position_model extends MY_Model {
	protected $tableName = 'employee_position';
	protected $resultMode = 'object';	  
	public $primaryKey = 'employee_id';	
	public $whereFields;
	protected $orderFields = array('a.employee_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>