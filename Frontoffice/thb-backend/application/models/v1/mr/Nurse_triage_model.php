<?php class Nurse_triage_model extends MY_Model {
	protected $tableName = 'nurse_triage';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>