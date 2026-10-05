<?php class Laboratory_request_items_model extends MY_Model {
	protected $tableName = 'laboratory_request_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>