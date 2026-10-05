<?php class Nurse_obgyn_special_model extends MY_Model {
	protected $tableName = 'nurse_obgyn_special';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>