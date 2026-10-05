<?php class Hemodialysis_package_model extends MY_Model {
	protected $tableName = 'hemodialysis_package';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,b.name as costing_name";
	protected $joinFields = array(
        array('costing b', 'a.costing_id = b.id', 'LEFT'),
	);
}?>