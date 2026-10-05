<?php class Transaction_invoice_model extends MY_Model {
	protected $tableName = 'transaction_invoice';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,e.fullname as cashier_name,e.ttd,p.fullname,p.norm";
	protected $joinFields = array(
		array('user u', 'a.cashier_id = u.id', 'LEFT'),
		array('registration r', 'a.registration_id = r.id', 'LEFT'),
		array('patient p', 'r.norm = p.norm', 'LEFT'),
		array('employee e', 'u.employee_id = e.id', 'LEFT'),
	);
	
	// protected $tableName = 'transaction_invoice';
	// protected $resultMode = 'object';	  
	// public $primaryKey = 'id';	
	// public $whereFields;
	// public $limit;
	// public $offset;
	// protected $orderFields = array();
	// protected $selectFields = "a.*,e.fullname as cashier_name,p.fullname,p.norm";
	// protected $joinFields = array(
	// 	array('user u', 'a.cashier_id = u.id', 'LEFT'),
	// 	array('registration r', 'a.registration_id = r.id', 'LEFT'),
	// 	array('registration_destination rd', 'r.id = rd.registration_id', 'LEFT'),
	// 	array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'LEFT'),
	// 	array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary ="1"', 'LEFT'),
	// 	array('patient p', 'r.norm = p.norm', 'LEFT'),
	// 	array('employee e', 'u.employee_id = e.id', 'LEFT'),
	// );
}?>