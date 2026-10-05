<?php class Costing_services_class_model extends MY_Model {
	protected $tableName = 'costing_services_class';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,c.name,hfi.name as health_facility_item_name,sc.name as service_class_name,co.name as company_name";
	protected $joinFields = array(
		array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
		array('service_class sc', 'a.service_class_id = sc.id', 'LEFT'),
		array('company co', 'a.company_insurance_id = co.id', 'LEFT'),
		array('costing c', 'a.costing_id = c.id', 'LEFT'),
	);
}?>