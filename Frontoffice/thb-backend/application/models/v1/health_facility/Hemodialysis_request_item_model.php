<?php class Hemodialysis_request_item_model extends MY_Model {
	protected $tableName = 'hemodialysis_request_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'hemodialysis_request_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.hemodialysis_request_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>