<?php class Sbar_model extends MY_Model {
	protected $tableName = 'sbar';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,e.fullname as doctor_in_charge_name, e.ttd as doctor_in_charge_ttd, s.doctor_in_charge_id as doctor_in_charge_soap_id, e2.fullname as doctor_in_charge_name_sbar, e2.ttd as doctor_in_charge_ttd_sbar";
	protected $joinFields = array(
		array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
		array('employee e2', 'a.doctor_in_charge_id_sbar = e2.id', 'LEFT'),
		array('soap s', 'a.soap_id = s.id', 'INNER'),
	);
}?>