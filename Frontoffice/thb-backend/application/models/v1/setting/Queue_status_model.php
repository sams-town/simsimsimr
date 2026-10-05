<?php class Queue_status_model extends MY_Model {
	protected $tableName = 'queue_status';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.status' => 'ASC');
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>