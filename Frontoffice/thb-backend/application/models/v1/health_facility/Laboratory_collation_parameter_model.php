<?php class Laboratory_collation_parameter_model extends MY_Model {
	protected $tableName = 'laboratory_collation_parameter';
	protected $resultMode = 'object';	  
	public $primaryKey = 'laboratory_collation_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.laboratory_collation_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>