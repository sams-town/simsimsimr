<?php class Costing_services_class_class_model extends MY_Model {
	protected $tableName = 'costing_services_class';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('c.name' => 'ASC');
	protected $selectFields = "a.*,c.name,c.is_ref, sc.name as service_class,c.type_costing_id,c.classification_one_type_id,c.classification_two_type_id,c.classification_three_type_id";
	protected $joinFields = array(
		array('costing c', 'a.costing_id = c.id', 'LEFT'),
		array('helper b', 'c.type_costing_id = b.code AND b.variable="type_costing"', 'LEFT'),
		array('service_class sc', 'a.service_class_id = sc.id', 'LEFT'),
	);
}?>