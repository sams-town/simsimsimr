<?php class Appointment_model extends MY_Model {
	protected $tableName = 'appointment';
	protected $resultMode = 'object';	  
	public $primaryKey = 'id';	
	public $whereFields;
	public $limit;
	public $offset;
	protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*, b.name as health_facility_name, c.name as health_facility_item_name, 
								d.fullname as doctor_in_charge_name,e.name as sex_name,f.queue,ds.code,f.doctor_schedule_item_id, cp.name as company_name,dsi.hour_start,dsi.hour_end";
	protected $joinFields = array(
        array('health_facility b', 'a.health_facility_id = b.id', 'LEFT'),
        array('health_facility_item c', 'a.health_facility_item_id = c.id', 'LEFT'),
        array('employee d', 'a.doctor_in_charge_id = d.id', 'LEFT'),
        array('helper e', 'a.sex = e.code AND variable="sex"', 'LEFT'),
        array('appointment_schedule f', 'a.id = f.appointment_id', 'LEFT'),
        array('doctor_schedule_item dsi', 'f.doctor_schedule_item_id = dsi.id', 'LEFT'),
        array('doctor_schedule ds', 'dsi.doctor_schedule_id = ds.id', 'LEFT'),
        array('company cp', 'a.insurance_id = cp.id', 'LEFT'),
    );

	public function filter_data($filter,$ssr = false) {
		$this->selectFields = "a.*, b.name as health_facility_name, c.name as health_facility_item_name, 
								d.fullname as doctor_in_charge_name,e.name as sex_name,f.queue,f.doctor_schedule_item_id, cp.name as company_name,dsi.hour_start,dsi.hour_end";
		$this->joinFields = array(
			array('health_facility b', 'a.health_facility_id = b.id', 'LEFT'),
			array('health_facility_item c', 'a.health_facility_item_id = c.id', 'LEFT'),
			array('employee d', 'a.doctor_in_charge_id = d.id', 'LEFT'),
			array('helper e', 'a.sex = e.code AND variable="sex"', 'LEFT'),
			array('appointment_schedule f', 'a.id = f.appointment_id', 'LEFT'),
        	array('doctor_schedule_item dsi', 'f.doctor_schedule_item_id = dsi.id', 'LEFT'),
			array('company cp', 'a.insurance_id = cp.id', 'LEFT'),
		);
		if(isset($filter['kodebooking'])) { $this->db->where('a.nobooking', $filter['kodebooking']); unset($filter['kodebooking']); }

		unset($filter['date_start']);
		unset($filter['date_end']);

		$this->db->where($filter);
		if($ssr) {
			return $this->read_ssr();
		} else {
			return $this->read();
		}
	}
}?>