<?php class Soap_s_model extends MY_Model {
	protected $tableName = 'soap_s';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,c.id as registration_destination_id";
	protected $joinFields = array(
		array('soap b', 'a.soap_id = b.id', 'LEFT'),
		array('registration_destination c', 'b.registration_destination_id = c.id', 'LEFT'),
	);
}?>