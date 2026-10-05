<?php class Physiotherapy_request_read_model extends MY_Model {
	protected $tableName = 'physiotherapy_request_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,h.date_request as date_added";
	protected $joinFields = array(
		array('physiotherapy_request h', 'a.physiotherapy_request_id = h.id', 'LEFT'),
		array('soap b', 'h.soap_id = b.id', 'LEFT'),
		array('registration_destination c', 'b.registration_destination_id = c.id', 'LEFT'),
		array('registration d', 'c.registration_id = d.id', 'LEFT'),
	);
}?>