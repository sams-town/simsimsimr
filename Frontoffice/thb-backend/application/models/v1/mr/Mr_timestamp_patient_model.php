<?php class Mr_timestamp_patient_model extends MY_Model {
	protected $tableName = 'mr_timestamp_patient';
	protected $resultMode = 'object';	  
	public $primaryKey = 'registration_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.registration_id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>