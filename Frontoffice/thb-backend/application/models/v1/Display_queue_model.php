<?php class Display_queue_model extends MY_Model {
	protected $tableName = 'queue';
	protected $resultMode = 'object';
	public $primaryKey = 'name';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.name' => 'ASC');
	protected $selectFields = "";
	protected $joinFields = array();
}?>