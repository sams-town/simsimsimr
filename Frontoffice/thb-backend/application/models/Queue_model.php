<?php class Queue_model extends MY_Model {
	protected $tableName = 'counter_queue';
	protected $resultMode = 'object';	  
	public $primaryKey = 'var_1';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.var_1' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>