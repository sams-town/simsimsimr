<?php class Costing_value_model extends MY_Model {
	protected $tableName = 'costing_value';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,c.costing_id";
	protected $joinFields = array(
		array('costing_services_class c', 'a.costing_services_class_id = c.id', 'LEFT'),
	);
}?>