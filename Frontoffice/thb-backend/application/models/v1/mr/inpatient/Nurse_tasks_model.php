<?php class Nurse_tasks_model extends MY_Model {
	protected $tableName = 'nurse_tasks';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>