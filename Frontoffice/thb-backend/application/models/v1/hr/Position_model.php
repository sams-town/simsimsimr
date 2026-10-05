<?php class Position_model extends MY_Model {
	protected $tableName = 'position';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*, hfi.health_facility_id, hfi.name as health_facility_item_name, d.name as department_name";
	protected $joinFields = array(
		array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
		array('department d', 'a.department_id = d.id', 'LEFT'),
	);
}?>