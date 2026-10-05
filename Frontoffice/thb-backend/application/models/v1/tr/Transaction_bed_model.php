<?php class Transaction_bed_model extends MY_Model {
	protected $tableName = 'transaction_bed';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array();
	protected $selectFields = "a.*,c.name as costing_name,cs.var_1,hfi.name as health_facility_item_name, tr.bed_id as bed_id_arrival, tr.arrival_time, tr.date_added as date_added_tr, tr.time_out, rd.checkin_date as checkin_date_reg, rd.checkout_date as checkout_date_reg";
	protected $joinFields = array(
		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
		array('treatment_room tr', 'a.registration_destination_id = tr.registration_destination_id AND a.bed_id = tr.bed_id', 'LEFT'),
		array('costing c', 'a.costing_id = c.id', 'LEFT'),
		array('bed b', 'a.bed_id = b.id', 'LEFT'),
		array('room r', 'b.room_id = r.id', 'LEFT'),
		array('health_facility_item hfi', 'r.health_facility_item_id = hfi.id', 'LEFT'),
		array('costing_services_class cs', 'a.costing_id = cs.costing_id', 'LEFT'),
	);
}?>