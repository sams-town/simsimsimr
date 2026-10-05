<?php class Costing_health_facility_item_model extends MY_Model {
	protected $tableName = 'costing_health_facility_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'costing_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields;
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>
