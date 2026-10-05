<?php class Initial_model extends MY_Model {
	protected $tableName = 'initial';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>