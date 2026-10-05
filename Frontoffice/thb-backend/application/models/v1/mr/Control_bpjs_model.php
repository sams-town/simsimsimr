<?php class Control_bpjs_model extends MY_Model {
	protected $tableName = 'control_bpjs';
	protected $resultMode = 'object';	  
	public $primaryKey = 'no_control';	
	public $whereFields;
	public $limit;
	public $offset;
	// protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,f.fullname as doctor_name, f.id as doctor_id, d.name as health_facility_item_name, d.id as health_facility_item_id,rd.exit_id";
	protected $joinFields = array(
		array('health_facility_item d', 'a.poliTujuan = d.bride_bpjs', 'LEFT'),
		array('employee f', 'a.kodeDokter = f.bride_bpjs', 'LEFT'),
		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
		// array('registration r', 'rd.registration_id = r.id', 'LEFT'),
		// array('helper b', 'a.insurance_id = b.code AND b.variable="insurance"', 'LEFT'),
		// array('company c', 'a.company_id = c.id', 'LEFT'),
	);

	// public function history() {
	// 	$this->selectFields = "a.*,b.name as insurance_name, c.name as company_name,r.icd10_id,r.icd10_code,r.icd10_name,rd.register_date";
	// 	$this->orderFields = array('r.register_date' => 'DESC');
	// 	$this->joinFields = array(
	// 		array('helper b', 'a.insurance_id = b.code AND b.variable="insurance"', 'LEFT'),
	// 		array('company c', 'a.company_id = c.id', 'LEFT'),
	// 		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
	// 		array('registration r', 'rd.registration_id = r.id', 'LEFT'),
	// 	);
	// }

	// public function noka() {
	// 	$this->selectFields = "a.*,b.name as insurance_name, c.name as company_name,r.icd10_id,r.icd10_code,r.icd10_name,rd.register_date, p.*,d.name as health_facility_item_name,
	// 							e.name as health_facility_name,e.id as health_facility_id,f.fullname as doctor_in_charge_name,rd.*";
	// 	$this->orderFields = array('r.register_date' => 'DESC');
	// 	$this->joinFields = array(
	// 		array('helper b', 'a.insurance_id = b.code AND b.variable="insurance"', 'LEFT'),
	// 		array('company c', 'a.company_id = c.id', 'LEFT'),
	// 		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
	// 		array('registration r', 'rd.registration_id = r.id', 'LEFT'),
	// 		array('patient p', 'r.norm = p.norm', 'LEFT'),
	// 		array('health_facility_item d', 'rd.health_facility_item_id = d.id', 'LEFT'),
	// 		array('health_facility e', 'd.health_facility_id = e.id', 'LEFT'),
	// 		array('employee f', 'rd.doctor_in_charge_id = f.id', 'LEFT'),
	// 	);
	// }
}?>