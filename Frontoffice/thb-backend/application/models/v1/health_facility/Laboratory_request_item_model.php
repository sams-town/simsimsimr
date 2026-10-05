<?php class Laboratory_request_item_model extends MY_Model {
	protected $tableName = 'laboratory_request_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'laboratory_request_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.laboratory_request_id' => 'ASC');
	protected $selectFields = "a.*,h.date_request as date_added,lp.code_lis,hfi.name as health_facility_item_name, csc.service_class_id";
	protected $joinFields = array(
		array('laboratory_request h', 'a.laboratory_request_id = h.id', 'LEFT'),
		array('laboratory_package lp', 'a.laboratory_package_id = lp.id', 'LEFT'),
		array('costing_services_class csc', 'lp.costing_id = csc.costing_id', 'LEFT'),
		array('soap b', 'h.soap_id = b.id', 'LEFT'),
		array('registration_destination c', 'b.registration_destination_id = c.id', 'LEFT'),
		array('health_facility_item hfi', 'c.health_facility_item_id = hfi.id', 'LEFT'),
		array('registration d', 'c.registration_id = d.id', 'LEFT'),
	);
}?>