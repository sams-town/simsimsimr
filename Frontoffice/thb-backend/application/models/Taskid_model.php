<?php class Taskid_model extends MY_Model {
	protected $tableName = 'taskid';
	protected $resultMode = 'object';	  
	public $primaryKey = 'code_booking';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.code_booking' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>