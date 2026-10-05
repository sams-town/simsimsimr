<?php class Nurse_adult_inpatient_model extends MY_Model {
	protected $tableName = 'nurse_adult_inpatient';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>