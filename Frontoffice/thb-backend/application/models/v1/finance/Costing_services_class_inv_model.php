<?php class Costing_services_class_inv_model extends MY_Model {
	protected $tableName = 'costing_services_class';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,c.name,hfi.name as health_facility_item_name,sc.name as service_class_name,co.name as company_name,
	c.type_costing_id,c.classification_one_type_id,c.classification_two_type_id,c.classification_three_type_id,
	ct.name as type_costing_name,
	c1.name as classification_one_type_name,
	c2.name as classification_two_type_name,
	c3.name as classification_three_type_name
	";
	protected $joinFields = array(
		array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
		array('service_class sc', 'a.service_class_id = sc.id', 'LEFT'),
		array('company co', 'a.company_insurance_id = co.id', 'LEFT'),
		array('costing c', 'a.costing_id = c.id', 'LEFT'),
		array('helper ct', 'c.type_costing_id = ct.code AND ct.variable="type_costing"', 'LEFT'),
		array('helper c1', 'c.classification_one_type_id = c1.code AND c1.variable="costing_c_one"', 'LEFT'),
		array('helper c2', 'c.classification_two_type_id = c2.code AND c2.variable="costing_c_one"', 'LEFT'),
		array('helper c3', 'c.classification_three_type_id = c3.code AND c3.variable="costing_c_one"', 'LEFT'),
	);
}?>