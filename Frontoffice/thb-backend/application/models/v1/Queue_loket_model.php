<?php class Queue_loket_model extends MY_Model {
	protected $tableName = 'queue_loket';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'ASC');
	protected $selectFields = "a.*, l.name as loket";
	protected $joinFields = array(
		array('loket l', 'a.loket_id = l.id', 'LEFT'),
	);
}?>