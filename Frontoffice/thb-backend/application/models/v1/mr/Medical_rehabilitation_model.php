<?php class Medical_rehabilitation_model extends MY_Model {
	protected $tableName = 'medical_rehabilitation';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*";
	protected $joinFields = array();
}?>