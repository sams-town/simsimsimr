<?php class Registration_insurance_model extends MY_Model {
	protected $tableName = 'registration_insurance';
	protected $resultMode = 'object';	  
	public $primaryKey = 'registration_destination_id';	
	public $whereFields;
	public $limit;
	public $offset;
	// protected $orderFields = array('a.date_added' => 'DESC');
	protected $selectFields = "a.*,b.name as insurance_name, c.name as company_name";
	protected $joinFields = array(
		array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
		array('registration r', 'rd.registration_id = r.id', 'LEFT'),
		array('helper b', 'a.insurance_id = b.code AND b.variable="insurance"', 'LEFT'),
		array('company c', 'a.company_id = c.id', 'LEFT'),
	);

	public function history() {
		$this->selectFields = "a.*,b.name as insurance_name, hfi.name as health_facility_name, c.name as company_name,rd.register_date";
		$this->orderFields = array('r.register_date' => 'DESC');
		$this->joinFields = array(
			array('helper b', 'a.insurance_id = b.code AND b.variable="insurance"', 'LEFT'),
			array('company c', 'a.company_id = c.id', 'LEFT'),
			array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
			array('registration r', 'rd.registration_id = r.id', 'LEFT'),
			array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'LEFT'),
		);
	}

	public function noka() {
		$this->selectFields = "a.*,b.name as insurance_name, c.name as company_name,rd.register_date, p.*,d.name as health_facility_item_name,
								e.name as health_facility_name,e.id as health_facility_id,f.fullname as doctor_in_charge_name,rd.*";
		$this->orderFields = array('r.register_date' => 'DESC');
		$this->joinFields = array(
			array('helper b', 'a.insurance_id = b.code AND b.variable="insurance"', 'LEFT'),
			array('company c', 'a.company_id = c.id', 'LEFT'),
			array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
			array('registration r', 'rd.registration_id = r.id', 'LEFT'),
			array('patient p', 'r.norm = p.norm', 'LEFT'),
			array('health_facility_item d', 'rd.health_facility_item_id = d.id', 'LEFT'),
			array('health_facility e', 'd.health_facility_id = e.id', 'LEFT'),
			array('employee f', 'rd.doctor_in_charge_id = f.id', 'LEFT'),
		);
	}

	public function delete_by_primary($registration_destination_id, $insurance_id, $is_primary) {
        $this->db->query("
		SELECT * 
		FROM registration_insurance 
		WHERE registration_destination_id = '$registration_destination_id'
		AND insurance_id = '$insurance_id'
		AND is_primary = '$is_primary'");

		$aff_row_select = $this->db->affected_rows();
		
		$this->db->query("
		DELETE FROM registration_insurance 
		WHERE registration_destination_id = '$registration_destination_id'
		AND insurance_id = '$insurance_id'
		AND is_primary = '$is_primary'");
		
		$aff_row_delete = $this->db->affected_rows();
		
		if ($aff_row_select != $aff_row_delete) {
			return 0;
		}

		return 1;
	}
}?>