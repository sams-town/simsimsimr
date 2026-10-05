<?php class Radiology_request_image_model extends MY_Model {
	protected $tableName = 'radiology_request_item_image';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>