<?php class Radiology_request_hpp_model extends MY_Model {
	protected $tableName = 'radiology_request_hpp';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*";
	protected $joinFields = array(
	);
}?>
