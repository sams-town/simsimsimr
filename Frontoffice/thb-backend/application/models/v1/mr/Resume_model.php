<?php class Resume_model extends MY_Model {
	protected $tableName = 'resume';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
    public $limit = 25;
	public $offset;
	public $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, r.register_date, hfi2.name as health_facility_item_name, hfic.name as health_facility_item_name_control, hfi2.name as destination_health_facility_item_name, e.fullname as doctor_in_charge_name, so.physical_check";
	protected $joinFields = array(
        array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
        array('registration r', 'rd.registration_id = r.id', 'INNER'),
        array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
		array('health_facility_item hfi2', 'rd.health_facility_item_id = hfi2.id', 'INNER'),
        array('health_facility_item hfic', 'a.health_facility_item_id = hfic.id', 'LEFT'),
        array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
        array('soap_o so', 'a.soap_id = so.soap_id', 'INNER'),
    );
}?>