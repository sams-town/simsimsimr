<?php class Physiotherapy_package_model extends MY_Model {
	protected $tableName = 'physiotherapy_package';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,b.name as costing_name";
	protected $joinFields = array(
        array('costing b', 'a.costing_id = b.id', 'LEFT'),
        array('costing_services_class csc', 'a.costing_id = csc.costing_id', 'LEFT'),
	);
}?>