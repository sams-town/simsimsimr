<?php class Period_model extends MY_Model {
	protected $tableName = 'b_period';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array(
		// array('helper b', 'a.type_id = b.code AND b.variable="type_classification"', 'LEFT'),
	);
}?>