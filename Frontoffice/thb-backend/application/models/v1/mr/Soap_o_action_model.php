<?php class Soap_o_action_model extends MY_Model {
	protected $tableName = 'soap_o_action';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>