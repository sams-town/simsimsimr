<?php class Soap_a_action_model extends MY_Model {
	protected $tableName = 'soap_a_action';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.index' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>