<?php class Radiology_request_item_model extends MY_Model {
	protected $tableName = 'radiology_request_item';
	protected $resultMode = 'object';	  
	public $primaryKey = 'radiology_request_id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.radiology_request_id' => 'ASC');
	protected $selectFields = "a.*,h.date_request as date_added,hfi.name as health_facility_item_name, csc.service_class_id";
	protected $joinFields = array(
		array('radiology_request h', 'a.radiology_request_id = h.id', 'LEFT'),
		array('radiology_package rp', 'a.radiology_package_id = rp.id', 'LEFT'),
		array('soap b', 'h.soap_id = b.id', 'LEFT'),
		array('costing_services_class csc', 'rp.costing_id = csc.costing_id', 'LEFT'),
		array('registration_destination c', 'b.registration_destination_id = c.id', 'LEFT'),
		array('health_facility_item hfi', 'c.health_facility_item_id = hfi.id', 'LEFT'),
		array('registration d', 'c.registration_id = d.id', 'LEFT'),
	);
}?>