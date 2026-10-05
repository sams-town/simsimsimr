<?php class Soap_o_model extends MY_Model {
	protected $tableName = 'soap_o';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>