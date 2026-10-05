<?php class Medical_check_up_model extends MY_Model {
	protected $tableName = 'medical_check_up';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>