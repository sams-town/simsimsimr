<?php class Patient_tasks_item_model extends MY_Model {
	protected $tableName = 'patient_tasks_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>