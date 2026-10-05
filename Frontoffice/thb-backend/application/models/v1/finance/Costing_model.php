<?php class Costing_model extends MY_Model {
	protected $tableName = 'costing';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,b.name as type_costing_name";
	protected $joinFields = array(
		array('helper b', 'a.type_costing_id = b.code AND b.variable="type_costing"', 'LEFT'),
	);
	// array('helper b', 'a.classification_one_type_id = b.code AND b.variable="costing_c_one"', 'LEFT'),
	}?>