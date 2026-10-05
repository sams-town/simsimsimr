<?php class Icd9cm_model extends MY_Model {
	protected $tableName = 'icd9cm';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	public $selectFields = "a.*,";
	protected $joinFields = array();
}?>