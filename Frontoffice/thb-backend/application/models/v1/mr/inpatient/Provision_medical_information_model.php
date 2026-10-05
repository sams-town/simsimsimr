<?php class Provision_medical_information_model extends MY_Model {
	protected $tableName = 'provision_medical_information';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	public $orderFields = array('a.id' => 'ASC');
	protected $selectFields = "a.*,";
	protected $joinFields = array();
}?>